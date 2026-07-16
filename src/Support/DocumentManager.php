<?php

declare(strict_types=1);

namespace Paperdoc\Support;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Contracts\LlmAugmenterInterface;
use Paperdoc\Document\Document;
use Paperdoc\Document\{Paragraph, Section, Table, TableCell, TableRow, TextRun};
use Paperdoc\Enum\Format;
use Paperdoc\Factory\{DocumentFactory, ParserFactory};
use Paperdoc\Llm\LlmAugmenter;
use Paperdoc\Ocr\{OcrManager, TesseractOcrProcessor};
use Paperdoc\Ocr\PostProcessing\PipelineFactory;

/**
 * Point d'entrée principal de Paperdoc.
 *
 * API unifiée : create(), open(), save(), renderAs(), convert().
 */
final class DocumentManager
{
    /* -------------------------------------------------------------
     | Create
     |------------------------------------------------------------- */

    /**
     * Crée un nouveau document vierge.
     *
     * @example $doc = DocumentManager::create('pdf');
     * @example $doc = DocumentManager::create(Format::PDF);
     */
    public static function create(Format|string $format, string $title = ''): DocumentInterface
    {
        return DocumentFactory::createDocument($format, $title);
    }

    /* -------------------------------------------------------------
     | Open / Parse
     |------------------------------------------------------------- */

    /**
     * Ouvre et parse un document existant.
     *
     * @param array{ocr?: bool|string, llm?: bool, language?: string} $options
     *   - ocr: true = force OCR, false = skip, 'auto' = auto-detect (default from config)
     *   - llm: true = enable LLM augmentation, false = skip (default from config)
     *   - language: OCR language code (default from config)
     *
     * @example $doc = DocumentManager::open('scan.pdf', ['ocr' => true, 'llm' => true]);
     */
    public static function open(string $filename, array $options = []): DocumentInterface
    {
        $parser = ParserFactory::getParser($filename);
        $document = $parser->parse($filename);
        $document->setMetadata('source_file', realpath($filename) ?: $filename);

        $config = self::resolveConfig();
        $ocr = Cast::asMap($config['ocr'] ?? null);
        $llm = Cast::asMap($config['llm'] ?? null);
        $ocrMode = $options['ocr'] ?? ($ocr['enabled'] ?? 'auto');
        $llmEnabled = $options['llm'] ?? ($llm['enabled'] ?? false);
        $language = Cast::asString($options['language'] ?? $ocr['language'] ?? 'auto', 'auto');

        if ($ocrMode === false) {
            return $document;
        }

        $ocrManager = self::buildOcrManager($config, $language);

        if (! $ocrManager->getProcessor()->isAvailable()) {
            return $document;
        }

        $llmAugmenter = Cast::asBool($llmEnabled) ? self::buildLlmAugmenter($config) : null;

        // Collect sections that need OCR
        $sectionsToProcess = [];
        foreach ($document->getSections() as $section) {
            $shouldProcess = $ocrMode === true || ($ocrMode === 'auto' && $ocrManager->needsOcr($section));

            if ($shouldProcess) {
                $sectionsToProcess[] = $section;
            }
        }

        if (empty($sectionsToProcess)) {
            return $document;
        }

        // Without LLM: batch all sections through the parallel pool
        if ($llmAugmenter === null) {
            $ocrLang = $language === 'auto' ? null : $language;
            $batchResults = $ocrManager->processSections($sectionsToProcess, $ocrLang);

            foreach ($batchResults as $idx => $ocrText) {
                if ($ocrText !== '') {
                    self::applySectionFromOcrText($sectionsToProcess[$idx], $ocrText);
                    $sectionsToProcess[$idx]->setMetadata('ocr_processed', true);
                }
            }
        } else {
            // With LLM: process sequentially (LLM calls can't be batched the same way)
            foreach ($sectionsToProcess as $section) {
                self::processSection($section, $ocrManager, $llmAugmenter, $language);
            }
        }

        return $document;
    }

    /**
     * Open multiple files in batch, running OCR in parallel across all documents.
     *
     * @param  string[] $filenames
     * @param  array{ocr?: bool|string, llm?: bool, language?: string} $options
     * @return DocumentInterface[]
     */
    public static function openBatch(array $filenames, array $options = []): array
    {
        $config = self::resolveConfig();
        $ocr = Cast::asMap($config['ocr'] ?? null);
        $ocrMode = $options['ocr'] ?? ($ocr['enabled'] ?? 'auto');
        $language = Cast::asString($options['language'] ?? $ocr['language'] ?? 'auto', 'auto');

        $documents = [];
        foreach ($filenames as $filename) {
            $parser = ParserFactory::getParser($filename);
            $doc = $parser->parse($filename);
            $doc->setMetadata('source_file', realpath($filename) ?: $filename);
            $documents[] = $doc;
        }

        if ($ocrMode === false) {
            return $documents;
        }

        $ocrManager = self::buildOcrManager($config, $language);

        if (! $ocrManager->getProcessor()->isAvailable()) {
            return $documents;
        }

        $allSections = [];
        $sectionDocMap = [];

        foreach ($documents as $docIdx => $document) {
            foreach ($document->getSections() as $section) {
                $shouldProcess = $ocrMode === true
                    || ($ocrMode === 'auto' && $ocrManager->needsOcr($section));

                if ($shouldProcess) {
                    $sectionDocMap[] = $docIdx;
                    $allSections[] = $section;
                }
            }
        }

        if (! empty($allSections)) {
            $ocrLang = $language === 'auto' ? null : $language;
            $batchResults = $ocrManager->processSections($allSections, $ocrLang);

            foreach ($batchResults as $idx => $ocrText) {
                if ($ocrText !== '') {
                    self::applySectionFromOcrText($allSections[$idx], $ocrText);
                    $allSections[$idx]->setMetadata('ocr_processed', true);
                }
            }
        }

        return $documents;
    }

    /* -------------------------------------------------------------
     | Save
     |------------------------------------------------------------- */

    /**
     * Sauvegarde un document dans le format spécifié.
     *
     * @param string|null $format Si null, utilise $document->getFormat()
     *
     * @example DocumentManager::save($doc, 'output.pdf', 'pdf');
     */
    public static function save(
        DocumentInterface $document,
        string $filename,
        Format|string|null $format = null,
    ): void {
        $format ??= $document->getFormat();
        $renderer = DocumentFactory::getRenderer($format);
        $renderer->save($document, $filename);
    }

    /* -------------------------------------------------------------
     | Render (string output without file)
     |------------------------------------------------------------- */

    /**
     * Rend un document dans le format spécifié et retourne le résultat en string.
     *
     * @example DocumentManager::renderAs($doc, 'md');
     */
    public static function renderAs(DocumentInterface $document, Format|string $format): string
    {
        return DocumentFactory::getRenderer($format)->render($document);
    }

    /* -------------------------------------------------------------
     | Convert
     |------------------------------------------------------------- */

    /**
     * Raccourci : ouvre + convertit + sauvegarde.
     *
     * @example DocumentManager::convert('data.csv', 'output.pdf', 'pdf');
     */
    /**
     * @param array{ocr?: bool|string, llm?: bool, language?: string} $options
     */
    public static function convert(
        string $sourceFile,
        string $targetFile,
        Format|string $targetFormat,
        array $options = [],
    ): void {
        $document = self::open($sourceFile, $options);
        self::save($document, $targetFile, $targetFormat);
    }

    /* -------------------------------------------------------------
     | String I/O
     |------------------------------------------------------------- */

    /**
     * Parse un document depuis son contenu brut, sans fichier source.
     *
     * @param array{ocr?: bool|string, llm?: bool, language?: string} $options
     *
     * @example $doc = DocumentManager::openString('# Title', Format::MD);
     */
    public static function openString(string $content, Format|string $format, array $options = []): DocumentInterface
    {
        $extension = $format instanceof Format ? $format->extension() : strtolower($format);
        $base = tempnam(sys_get_temp_dir(), 'paperdoc_str_');
        $tmp  = $base . '.' . $extension;

        try {
            file_put_contents($tmp, $content);
            $document = self::open($tmp, $options);
            $document->setMetadata('source_file', '');

            return $document;
        } finally {
            @unlink($tmp);
            @unlink($base);
        }
    }

    /**
     * Convertit un contenu brut d'un format vers un autre, en mémoire.
     *
     * @param  array{ocr?: bool|string, llm?: bool, language?: string}  $options
     *
     * @example $html = DocumentManager::convertString('# Title', 'md', 'html');
     */
    public static function convertString(
        string $content,
        Format|string $from,
        Format|string $to,
        array $options = [],
    ): string {
        return self::renderAs(self::openString($content, $from, $options), $to);
    }

    public static function openJson(string $json): DocumentInterface
    {
        return Document::fromJson($json);
    }

    public static function toJson(DocumentInterface $document): string
    {
        return json_encode($document, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /* -------------------------------------------------------------
     | Thumbnail (dynamic)
     |------------------------------------------------------------- */

    /**
     * Get a thumbnail from the first image found in the document.
     * Always recomputes from current image data – no file saved.
     *
     * @return array{data: string, mimeType: string, width: int, height: int}|null
     */
    public static function thumbnail(
        DocumentInterface $document,
        int $maxWidth = ThumbnailGenerator::DEFAULT_WIDTH,
        int $maxHeight = ThumbnailGenerator::DEFAULT_HEIGHT,
        int $quality = ThumbnailGenerator::DEFAULT_QUALITY,
    ): ?array {
        return $document->getThumbnail($maxWidth, $maxHeight, $quality);
    }

    /**
     * Get a thumbnail as a data URI string (ready for <img src="...">).
     */
    public static function thumbnailDataUri(
        DocumentInterface $document,
        int $maxWidth = ThumbnailGenerator::DEFAULT_WIDTH,
        int $maxHeight = ThumbnailGenerator::DEFAULT_HEIGHT,
        int $quality = ThumbnailGenerator::DEFAULT_QUALITY,
    ): ?string {
        return $document->getThumbnailDataUri($maxWidth, $maxHeight, $quality);
    }

    /**
     * Get a thumbnail as a raw base64 string (no data URI prefix).
     */
    public static function thumbnailBase64(
        DocumentInterface $document,
        int $maxWidth = ThumbnailGenerator::DEFAULT_WIDTH,
        int $maxHeight = ThumbnailGenerator::DEFAULT_HEIGHT,
        int $quality = ThumbnailGenerator::DEFAULT_QUALITY,
    ): ?string {
        return $document->getThumbnailBase64($maxWidth, $maxHeight, $quality);
    }

    /* -------------------------------------------------------------
     | Renderer/Parser Registration
     |------------------------------------------------------------- */

    /**
     * @param class-string<\Paperdoc\Contracts\RendererInterface> $rendererClass
     */
    public static function registerRenderer(string $format, string $rendererClass): void
    {
        DocumentFactory::registerRenderer($format, $rendererClass);
    }

    public static function registerParser(\Paperdoc\Contracts\ParserInterface $parser): void
    {
        ParserFactory::registerParser($parser);
    }

    /* -------------------------------------------------------------
     | OCR + LLM Pipeline (internal)
     |------------------------------------------------------------- */

    private static function processSection(
        Section $section,
        OcrManager $ocrManager,
        ?LlmAugmenterInterface $llmAugmenter,
        string $language,
    ): void {
        $ocrText = $ocrManager->processSection($section, $language === 'auto' ? null : $language);

        if ($ocrText === '' && $llmAugmenter === null) {
            return;
        }

        if ($llmAugmenter !== null) {
            $imagePath = self::extractFirstImagePath($section, $ocrManager);

            try {
                $structured = $llmAugmenter->structureDocument($ocrText, $imagePath, [
                    'language' => $language,
                ]);
            } finally {
                if ($imagePath !== null) {
                    @unlink($imagePath);
                }
            }

            self::applySectionFromStructured($section, $structured);
            $section->setMetadata('ocr_confidence', $structured['confidence']);
        } else {
            self::applySectionFromOcrText($section, $ocrText);
        }

        $section->setMetadata('ocr_processed', true);
    }

    /**
     * Save the first embedded image to a temp file for LLM vision input.
     */
    private static function extractFirstImagePath(Section $section, OcrManager $ocrManager): ?string
    {
        foreach ($section->getElements() as $element) {
            if ($element instanceof \Paperdoc\Document\Image && $element->hasData()) {
                return $ocrManager->saveImageToTemp($element);
            }
        }

        return null;
    }

    /**
     * @param array{title: string, paragraphs: string[], tables: array<int, string[][]>, confidence: float} $structured
     */
    private static function applySectionFromStructured(Section $section, array $structured): void
    {
        $section->clearElements();

        if ($structured['title'] !== '') {
            $section->addHeading($structured['title'], 1);
        }

        foreach ($structured['paragraphs'] as $text) {
            if (trim($text) !== '') {
                $section->addText($text);
            }
        }

        foreach ($structured['tables'] as $tableData) {
            if ($tableData === []) {
                continue;
            }

            $table = new Table();
            $isFirst = true;

            foreach ($tableData as $rowData) {
                $row = new TableRow();

                if ($isFirst) {
                    $row->setHeader();
                }

                foreach ($rowData as $cellText) {
                    $cell = new TableCell();
                    $cell->addElement(
                        (new Paragraph())->addRun(new TextRun($cellText))
                    );
                    $row->addCell($cell);
                }

                $table->addRow($row);
                $isFirst = false;
            }

            $section->addElement($table);
        }
    }

    private static function applySectionFromOcrText(Section $section, string $ocrText): void
    {
        $section->clearElements();

        foreach (preg_split('/\n{2,}/', $ocrText) ?: [] as $block) {
            $text = trim($block);

            if ($text !== '') {
                $section->addText($text);
            }
        }

        $section->setMetadata('ocr_processed', true);
    }

    /**
     * @return array<string, mixed>
     */
    private static function resolveConfig(): array
    {
        if (function_exists('config')) {
            /** @var mixed $config */
            $config = config('paperdoc', []);

            return Cast::asMap($config);
        }

        $configPath = __DIR__ . '/../../config/paperdoc.php';

        if (file_exists($configPath)) {
            /** @var mixed $config */
            $config = require $configPath;

            return Cast::asMap($config);
        }

        return [];
    }

    /**
     * @param array<string, mixed> $config
     */
    private static function buildOcrManager(array $config, string $language): OcrManager
    {
        $ocrConfig = Cast::asMap($config['ocr'] ?? null);
        $tesseractConfig = Cast::asMap($ocrConfig['tesseract'] ?? null);

        $binary = Cast::asString($tesseractConfig['binary'] ?? null, 'tesseract');
        $rawOptions = $tesseractConfig['options'] ?? [];
        $options = [];
        if (is_array($rawOptions)) {
            foreach ($rawOptions as $option) {
                if (is_string($option)) {
                    $options[] = $option;
                }
            }
        }
        $minTextRatio = Cast::asFloat($ocrConfig['min_text_ratio'] ?? null, 0.1);

        $ppConfig = Cast::asMap($ocrConfig['post_processing'] ?? null);
        $pipeline = PipelineFactory::fromConfig($ppConfig);

        $poolSize = $ocrConfig['pool_size'] ?? 0;
        if ($poolSize === 'auto') {
            $poolSize = 0;
        }
        $processTimeout = Cast::asInt($ocrConfig['process_timeout'] ?? null, 60);

        $processor = new TesseractOcrProcessor($binary, $options);

        return new OcrManager(
            $processor,
            $language,
            $minTextRatio,
            $pipeline,
            Cast::asInt($poolSize),
            $processTimeout,
        );
    }

    /**
     * @param array<string, mixed> $config
     */
    private static function buildLlmAugmenter(array $config): ?LlmAugmenterInterface
    {
        $llmConfig = Cast::asMap($config['llm'] ?? null);

        if (Cast::asString($llmConfig['api_key'] ?? null) === ''
            && Cast::asString($llmConfig['provider'] ?? null, 'openai') !== 'ollama'
        ) {
            return null;
        }

        return new LlmAugmenter($llmConfig);
    }
}
