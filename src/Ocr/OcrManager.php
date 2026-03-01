<?php

declare(strict_types=1);

namespace Paperdoc\Ocr;

use Paperdoc\Contracts\OcrProcessorInterface;
use Paperdoc\Document\Image;
use Paperdoc\Document\Section;
use Paperdoc\Ocr\PostProcessing\PostProcessingPipeline;

class OcrManager
{
    private OcrProcessorInterface $processor;

    private string $language;

    private float $minTextRatio;

    private ?string $detectedLanguage = null;

    private ?PostProcessingPipeline $pipeline = null;

    private int $poolSize;

    private int $processTimeout;

    private const SCRIPT_LANGUAGE_MAP = [
        'latin'      => 'eng',
        'arabic'     => 'ara',
        'cyrillic'   => 'rus',
        'han'        => 'chi_sim',
        'hangul'     => 'kor',
        'japanese'   => 'jpn',
        'devanagari' => 'hin',
        'greek'      => 'ell',
        'hebrew'     => 'heb',
        'thai'       => 'tha',
        'georgian'   => 'kat',
        'armenian'   => 'hye',
    ];

    /**
     * Diacritics that are strong indicators for a specific Latin-script language.
     * Each entry maps a regex character class to a Tesseract language code.
     *
     * @var array<string, string>
     */
    private const DIACRITIC_SIGNATURES = [
        '/[çéèêëàâùûîïôœæ]/u' => 'fra',
        '/[äöüß]/u'           => 'deu',
        '/[ñ¿¡]/u'            => 'spa',
        '/[ãõ]/u'             => 'por',
        '/[ąćęłńóśźż]/u'     => 'pol',
        '/[ăâîșț]/u'          => 'ron',
        '/[åæø]/u'            => 'nor',
    ];

    /**
     * @param OcrProcessorInterface $processor      OCR driver
     * @param string                $language        Language code or 'auto' for detection
     * @param float                 $minTextRatio    Threshold for scanned-page detection
     * @param int                   $poolSize        Max concurrent OCR processes (0 = auto-detect)
     * @param int                   $processTimeout  Timeout per OCR process in seconds
     */
    public function __construct(
        OcrProcessorInterface $processor,
        string $language = 'auto',
        float $minTextRatio = 0.1,
        ?PostProcessingPipeline $pipeline = null,
        int $poolSize = 0,
        int $processTimeout = 60,
    ) {
        $this->processor = $processor;
        $this->language = $language;
        $this->minTextRatio = $minTextRatio;
        $this->pipeline = $pipeline;
        $this->poolSize = $poolSize > 0 ? $poolSize : ProcessPool::detectCpuCores();
        $this->processTimeout = $processTimeout;
    }

    public function getProcessor(): OcrProcessorInterface
    {
        return $this->processor;
    }

    public function getDetectedLanguage(): ?string
    {
        return $this->detectedLanguage;
    }

    public function setPipeline(?PostProcessingPipeline $pipeline): void
    {
        $this->pipeline = $pipeline;
    }

    public function getPipeline(): ?PostProcessingPipeline
    {
        return $this->pipeline;
    }

    // ──────────────────────────────────────────────────────────────
    //  Scanned-page detection
    // ──────────────────────────────────────────────────────────────

    public function needsOcr(Section $section): bool
    {
        $elements = $section->getElements();

        if (empty($elements)) {
            return false;
        }

        $textCount = 0;
        $imageCount = 0;

        foreach ($elements as $element) {
            if ($element->getType() === 'paragraph') {
                $textCount++;
            } elseif ($element->getType() === 'image') {
                $imageCount++;
            }
        }

        if ($imageCount === 0) {
            return false;
        }

        $total = count($elements);

        return $total > 0 && ($textCount / $total) < $this->minTextRatio;
    }

    // ──────────────────────────────────────────────────────────────
    //  Language detection
    // ──────────────────────────────────────────────────────────────

    /**
     * Resolve the language to use for OCR.
     *
     * When configured as 'auto', the first image of the section is sampled:
     *   1. Script detection (Tesseract --psm 0) to identify the writing system
     *   2. For Latin script, a quick OCR pass + diacritic frequency analysis
     *      narrows it down to a specific language (fra, deu, spa, …)
     */
    public function resolveLanguage(Section $section): string
    {
        if ($this->language !== 'auto') {
            return $this->language;
        }

        if ($this->detectedLanguage !== null) {
            return $this->detectedLanguage;
        }

        $firstImage = $this->findFirstImage($section);
        if ($firstImage === null) {
            $this->detectedLanguage = 'eng';

            return 'eng';
        }

        $tmpPath = $this->saveImageToTemp($firstImage);

        try {
            $this->detectedLanguage = $this->detectFromImage($tmpPath);
        } finally {
            @unlink($tmpPath);
        }

        return $this->detectedLanguage;
    }

    private function detectFromImage(string $imagePath): string
    {
        // Step 1: always run a Latin-based sample — 'eng' is universally available
        $sample = '';
        try {
            $sample = $this->processor->recognize($imagePath, 'eng');
        } catch (\Throwable) {
            // ignore
        }

        // Step 2: if sample has enough text, analyse diacritics to pick the language
        if (mb_strlen($sample) >= 20) {
            $detected = self::detectLanguageFromText($sample);

            // If a specific Latin language is identified, use it
            if ($detected !== 'eng') {
                return $detected;
            }

            // Check if the sample looks like real Latin text
            $latinLetters = preg_match_all('/[a-zA-ZÀ-ÿ]/u', $sample);
            $totalLetters = preg_match_all('/\\p{L}/u', $sample);

            if ($totalLetters > 0 && $latinLetters / $totalLetters > 0.6) {
                return 'eng';
            }
        }

        // Step 3: OSD fallback for non-Latin scripts
        $scriptInfo = $this->processor->detectScript($imagePath);
        $script = $scriptInfo['script'] ?? null;
        $confidence = $scriptInfo['confidence'] ?? 0.0;

        if ($script !== null && $confidence >= 5.0) {
            $normalized = strtolower($script);
            if (isset(self::SCRIPT_LANGUAGE_MAP[$normalized])) {
                return self::SCRIPT_LANGUAGE_MAP[$normalized];
            }
        }

        return 'eng';
    }

    /**
     * Score text by diacritic frequency to identify the Latin-script language.
     */
    public static function detectLanguageFromText(string $text): string
    {
        if (mb_strlen($text) < 10) {
            return 'eng';
        }

        $best = 'eng';
        $bestScore = 0;

        foreach (self::DIACRITIC_SIGNATURES as $pattern => $lang) {
            $score = preg_match_all($pattern, $text);
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $lang;
            }
        }

        return $best;
    }

    // ──────────────────────────────────────────────────────────────
    //  Section / image processing
    // ──────────────────────────────────────────────────────────────

    /**
     * Run OCR on all embedded images in a section and return the combined text.
     *
     * When multiple images are found, they are processed in parallel
     * using a ProcessPool for significantly faster throughput.
     */
    public function processSection(Section $section, ?string $language = null): string
    {
        $language ??= $this->resolveLanguage($section);

        $images = [];
        foreach ($section->getElements() as $element) {
            if ($element instanceof Image && $element->hasData()) {
                $images[] = $element;
            }
        }

        if (empty($images)) {
            return '';
        }

        $texts = count($images) === 1
            ? [$this->processImage($images[0], $language)]
            : $this->processImagesParallel($images, $language);

        return $this->postProcess(
            implode("\n\n", array_filter($texts, fn (string $t) => $t !== '')),
            $language,
        );
    }

    /**
     * Process multiple sections in parallel using a single ProcessPool.
     *
     * All images from all sections are submitted to the pool at once,
     * then results are grouped back per section and post-processed.
     *
     * @param  Section[] $sections
     * @return array<int, string> section index => processed text
     */
    public function processSections(array $sections, ?string $language = null): array
    {
        $language ??= $this->resolveLanguageFromSections($sections);

        $pool = new ProcessPool($this->poolSize, $this->processTimeout);
        $tmpPaths = [];
        $sectionImageKeys = [];

        $jobId = 0;
        foreach ($sections as $sIdx => $section) {
            $sectionImageKeys[$sIdx] = [];

            foreach ($section->getElements() as $element) {
                if (! ($element instanceof Image) || ! $element->hasData()) {
                    continue;
                }

                $tmpPath = $this->saveImageToTemp($element);
                $tmpPaths[$jobId] = $tmpPath;
                $key = (string) $jobId;

                $pool->submit($this->processor->buildCommand($tmpPath, $language), $key);
                $sectionImageKeys[$sIdx][] = $key;
                $jobId++;
            }
        }

        if ($jobId === 0) {
            return array_fill_keys(array_keys($sectionImageKeys), '');
        }

        try {
            $results = $pool->run();
        } finally {
            foreach ($tmpPaths as $path) {
                @unlink($path);
            }
        }

        $output = [];
        foreach ($sectionImageKeys as $sIdx => $keys) {
            $texts = [];
            foreach ($keys as $key) {
                $text = $results[$key] ?? '';
                if ($text !== '') {
                    $texts[] = $text;
                }
            }

            $output[$sIdx] = $this->postProcess(implode("\n\n", $texts), $language);
        }

        return $output;
    }

    /**
     * Apply noise cleaning and post-processing pipeline.
     */
    private function postProcess(string $text, string $language): string
    {
        if ($text === '') {
            return '';
        }

        $cleaned = self::cleanOcrNoise($text);

        if ($this->pipeline !== null) {
            $context = ['language' => $language];
            $cleaned = $this->pipeline->process($cleaned, $context);
        }

        return $cleaned;
    }

    /**
     * Process multiple images in parallel using the ProcessPool.
     *
     * @param  Image[] $images
     * @return string[]
     */
    private function processImagesParallel(array $images, string $language): array
    {
        $pool = new ProcessPool($this->poolSize, $this->processTimeout);
        $tmpPaths = [];

        foreach ($images as $i => $image) {
            $tmpPath = $this->saveImageToTemp($image);
            $tmpPaths[$i] = $tmpPath;
            $cmd = $this->processor->buildCommand($tmpPath, $language);
            $pool->submit($cmd, (string) $i);
        }

        try {
            $results = $pool->run();
        } finally {
            foreach ($tmpPaths as $path) {
                @unlink($path);
            }
        }

        $ordered = [];
        for ($i = 0, $count = count($images); $i < $count; $i++) {
            $ordered[] = $results[(string) $i] ?? '';
        }

        return $ordered;
    }

    /**
     * Resolve language from the first image across multiple sections.
     */
    private function resolveLanguageFromSections(array $sections): string
    {
        if ($this->language !== 'auto') {
            return $this->language;
        }

        if ($this->detectedLanguage !== null) {
            return $this->detectedLanguage;
        }

        foreach ($sections as $section) {
            $firstImage = $this->findFirstImage($section);
            if ($firstImage !== null) {
                $tmpPath = $this->saveImageToTemp($firstImage);

                try {
                    $this->detectedLanguage = $this->detectFromImage($tmpPath);
                } finally {
                    @unlink($tmpPath);
                }

                return $this->detectedLanguage;
            }
        }

        $this->detectedLanguage = 'eng';

        return 'eng';
    }

    public function processImage(Image $image, ?string $language = null): string
    {
        if (! $image->hasData()) {
            return '';
        }

        $language ??= $this->language === 'auto' ? ($this->detectedLanguage ?? 'eng') : $this->language;
        $tmpPath = $this->saveImageToTemp($image);

        try {
            return $this->processor->recognize($tmpPath, $language);
        } finally {
            @unlink($tmpPath);
        }
    }

    public function saveImageToTemp(Image $image): string
    {
        $ext = match ($image->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/tiff' => 'tiff',
            'image/bmp'  => 'bmp',
            default      => 'png',
        };

        $tmpPath = sys_get_temp_dir() . '/paperdoc_ocr_' . uniqid() . '.' . $ext;

        if (file_put_contents($tmpPath, $image->getData()) === false) {
            throw new \RuntimeException("Impossible d'écrire le fichier temporaire : {$tmpPath}");
        }

        return $tmpPath;
    }

    // ──────────────────────────────────────────────────────────────
    //  Generic noise filtering (language-agnostic)
    // ──────────────────────────────────────────────────────────────

    /**
     * Remove noise lines typically produced by OCR on logos,
     * stamps, signatures, borders and other graphical elements.
     *
     * Every heuristic here is purely statistical / structural —
     * no dictionaries, no bigram tables, no language-specific data.
     */
    public static function cleanOcrNoise(string $text): string
    {
        $lines = explode("\n", $text);
        $cleaned = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                $cleaned[] = '';

                continue;
            }

            if (self::isNoiseLine($trimmed)) {
                continue;
            }

            $cleaned[] = self::cleanLineEdges($trimmed);
        }

        $result = preg_replace('/\n{3,}/', "\n\n", implode("\n", $cleaned));

        return trim($result);
    }

    // ──────────────────────────────────────────────────────────────
    //  Noise detection — every check is language-agnostic
    // ──────────────────────────────────────────────────────────────

    private static function isNoiseLine(string $line): bool
    {
        $letters = preg_replace('/[^\\p{L}]/u', '', $line);
        $letterCount = mb_strlen($letters);
        $lineLen = mb_strlen($line);

        // ── absolute minimums ────────────────────────────────────
        if ($letterCount <= 2) {
            return true;
        }
        if ($lineLen < 8) {
            return true;
        }

        // ── letter density: garbled lines are symbol/digit-heavy ─
        $nonSpaceChars = mb_strlen(preg_replace('/\s/u', '', $line));
        if ($nonSpaceChars > 0 && ($letterCount / $nonSpaceChars) < 0.4) {
            return true;
        }

        // ── character diversity (catches "NDDODODOMONON", "OOOIOOOCOI") ─
        if ($letterCount >= 5 && $letterCount <= 30) {
            $chars = mb_str_split(mb_strtolower($letters));
            $unique = count(array_unique($chars));
            $diversity = $unique / count($chars);

            if ($diversity < 0.40) {
                return true;
            }
            if (preg_match('/(.)\1{2,}/u', mb_strtolower($letters)) && $diversity < 0.50) {
                return true;
            }
        }

        // ── no actual word (3+ consecutive letters) ─────────────
        if (! preg_match('/\\p{L}{3,}/u', $line)) {
            return true;
        }

        // ── noise symbols (brackets, pipes, math, …) ────────────
        $symbolCount = preg_match_all('/[|()[\]<>{}+=&#@*^~\\\\]/u', $line);
        if ($symbolCount >= 2 && $lineLen < 40) {
            return true;
        }
        if ($symbolCount >= 3) {
            return true;
        }

        // ── bracket/paren glued to a letter in short lines ──────
        $words = preg_split('/\s+/', $line);
        $wordCount = count($words);
        if ($lineLen < 30 && preg_match('/\\p{L}[)\]]/u', $line) && $wordCount <= 5) {
            return true;
        }

        // ── token fragmentation ─────────────────────────────────
        if ($wordCount >= 4) {
            $singleCharTokens = 0;
            foreach ($words as $w) {
                if (mb_strlen($w) === 1) {
                    $singleCharTokens++;
                }
            }
            if ($singleCharTokens / $wordCount > 0.4) {
                return true;
            }
        }

        // ── short lines: every word must look like a natural word ─
        if ($lineLen < 25 && ! self::hasNaturalWords($line)) {
            return true;
        }

        // ── medium lines with many garbled tokens ───────────────
        if ($wordCount >= 4 && $lineLen < 50) {
            $garbled = 0;
            $checked = 0;
            foreach ($words as $w) {
                $clean = preg_replace('/[^\\p{L}]/u', '', $w);
                $wLen = mb_strlen($clean);
                if ($wLen < 3) {
                    continue;
                }
                $checked++;
                if (! self::isNaturalWord($clean)) {
                    $garbled++;
                }
            }
            if ($checked >= 3 && $garbled / $checked > 0.5) {
                return true;
            }
        }

        return false;
    }

    // ──────────────────────────────────────────────────────────────
    //  Word naturalness (language-agnostic)
    //
    //  Real words in virtually all alphabetic scripts share:
    //   • a mix of vowels and consonants
    //   • reasonable character diversity
    //   • no long runs of identical characters
    // ──────────────────────────────────────────────────────────────

    private static function isNaturalWord(string $word): bool
    {
        $lower = mb_strtolower($word);
        $len = mb_strlen($lower);

        if ($len < 3) {
            return true;
        }

        $vowels = preg_match_all('/[aeiouyàáâãäåèéêëìíîïòóôõöùúûüýÿæœ]/u', $lower);
        $consonants = preg_match_all('/[bcdfghjklmnpqrstvwxzçñß]/u', $lower);
        $total = $vowels + $consonants;

        if ($total === 0) {
            return false;
        }

        // Words with no vowels or no consonants are suspicious
        if ($vowels === 0 || $consonants === 0) {
            return $len <= 3;
        }

        // Extreme vowel ratios are suspicious
        $vRatio = $vowels / $total;
        if ($vRatio < 0.10 || $vRatio > 0.90) {
            return false;
        }

        // Low character diversity within the word
        if ($len >= 4) {
            $chars = mb_str_split($lower);
            $unique = count(array_unique($chars));
            if ($unique / $len < 0.40) {
                return false;
            }
        }

        // Consecutive identical characters (≥ 3)
        if (preg_match('/(.)\1{2,}/u', $lower)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a line has at least one word that looks like natural language.
     */
    private static function hasNaturalWords(string $line): bool
    {
        $lineLen = mb_strlen($line);
        $words = preg_split('/\s+/', $line);
        $checked = 0;
        $natural = 0;

        foreach ($words as $w) {
            $clean = preg_replace('/[^\\p{L}]/u', '', $w);
            if (mb_strlen($clean) < 3) {
                continue;
            }
            $checked++;
            if (self::isNaturalWord($clean)) {
                $natural++;
            }
        }

        if ($checked === 0) {
            return false;
        }

        if ($lineLen < 15) {
            return $natural >= $checked;
        }

        return $natural > 0;
    }

    // ──────────────────────────────────────────────────────────────
    //  Edge cleaning — strip OCR artefacts at line boundaries
    // ──────────────────────────────────────────────────────────────

    private static function cleanLineEdges(string $line): string
    {
        // Leading single digit before a letter (margin artefact)
        $line = preg_replace('/^\d\s+(?=\\p{L})/u', '', $line);
        // Leading parenthesized uppercase prefix "(G ", "(A "
        $line = preg_replace('/^\(\\p{Lu}{1,2}\s+/u', '', $line);
        // Leading quoted single char: "Ü …
        $line = preg_replace('/^["«»]\\p{L}{1,2}\s+/u', '', $line);
        // Leading 1-3 non-letter/digit chars + space
        $line = preg_replace('/^[^\\p{L}\\d]{1,3}\s+/u', '', $line);
        // Leading 1-3 lowercase chars before uppercase word
        $line = preg_replace('/^\\p{Ll}{1,3}\s+(?=\\p{Lu})/u', '', $line);
        // Leading single uppercase letter + space before another letter
        $line = preg_replace('/^\\p{Lu}\s+(?=\\p{L})/u', '', $line);
        // Leading 2-letter all-caps prefix before a Titlecase word
        $line = preg_replace('/^\\p{Lu}{2}\s+(?=\\p{Lu}\\p{Ll})/u', '', $line);

        // Garbled prefix before a valid capitalised word in long lines
        if (mb_strlen($line) > 30) {
            $line = self::stripGarbledPrefix($line);
        }

        // Trailing noise symbols + optional digits/punct
        $line = preg_replace('/\s*[|(\[<>)\]+=#&"]{1,2}\s*[\d\p{P}\s]{0,5}$/u', '', $line);
        // Trailing isolated single letter or digit
        $line = preg_replace('/\s+(?:\\p{L}{1,2}|\d)$/u', '', $line);

        return trim($line);
    }

    /**
     * If the line starts with short garbled tokens followed by a valid
     * capitalised word, drop the garbled prefix.
     */
    private static function stripGarbledPrefix(string $line): string
    {
        return preg_replace_callback(
            '/^((?:\S{1,8}\s+){2,5})(\\p{Lu}\\p{Ll}{3,})/u',
            function (array $m): string {
                $tokens = preg_split('/\s+/', trim($m[1]));
                $garbled = 0;
                $checked = 0;

                foreach ($tokens as $tok) {
                    $clean = preg_replace('/[^\\p{L}]/u', '', $tok);
                    $len = mb_strlen($clean);

                    if ($len === 0) {
                        continue;
                    }
                    if ($len <= 2) {
                        continue; // skip short function words in any language
                    }

                    $checked++;

                    if (! self::isNaturalWord($clean)) {
                        $garbled++;
                    }
                }

                if ($checked >= 2 && $garbled / $checked >= 0.6) {
                    return $m[2];
                }

                return $m[0];
            },
            $line,
        );
    }

    // ──────────────────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────────────────

    private function findFirstImage(Section $section): ?Image
    {
        foreach ($section->getElements() as $element) {
            if ($element instanceof Image && $element->hasData()) {
                return $element;
            }
        }

        return null;
    }
}
