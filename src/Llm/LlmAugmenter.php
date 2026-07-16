<?php

declare(strict_types=1);

namespace Paperdoc\Llm;

use Paperdoc\Contracts\LlmAugmenterInterface;
use Paperdoc\Contracts\LlmProviderInterface;
use Paperdoc\Exceptions\LlmException;
use Paperdoc\Llm\Dto\PageContent;
use Paperdoc\Support\Cast;

class LlmAugmenter implements LlmAugmenterInterface
{
    private const STRUCTURE_MAX_ATTEMPTS = 3;

    private const SYSTEM_PROMPT = <<<'PROMPT'
You are a document analysis assistant specialized in OCR post-processing. Your tasks:

1. CORRECT OCR errors: fix typos, broken words, garbled characters, and encoding artifacts.
2. STRUCTURE content: identify titles/headings, paragraphs, lists, and tables.
3. PRESERVE meaning: never add, remove, or rewrite content — only fix OCR mistakes.
4. DETECT tables: if tabular data is present, extract it as structured rows and columns.
5. ASSESS confidence: rate your confidence in the output quality from 0.0 to 1.0.

When given an image of a page along with raw OCR text:
- Use the image as the ground truth for layout and content.
- Use the OCR text as a noisy starting point to correct from.
- If the image is clear but OCR text is missing/empty, transcribe directly from the image.

Always respond in the same language as the document content.
PROMPT;

    private LlmProviderInterface $provider;

    /** @var array<string, mixed> */
    private array $options;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config, ?LlmProviderInterface $provider = null)
    {
        $this->provider = $provider ?? ProviderFactory::make($config);
        $this->options  = Cast::asMap($config['options'] ?? []);
    }

    public function enhance(string $rawText, array $options = []): string
    {
        if (trim($rawText) === '') {
            return '';
        }

        $answer = $this->provider->chat(
            self::SYSTEM_PROMPT,
            [['type' => 'text', 'text' =>
                "Correct the following OCR text. Fix errors and clean up formatting. "
                . "Return ONLY the corrected text, nothing else.\n\n"
                . "---\n{$rawText}\n---",
            ]],
            $options + $this->options,
        );

        return trim($answer);
    }

    public function structureDocument(string $rawText, ?string $imagePath = null, array $options = []): array
    {
        $content = [];

        if ($imagePath !== null && file_exists($imagePath)) {
            $content[] = [
                'type'     => 'image',
                'data'     => base64_encode((string) file_get_contents($imagePath)),
                'mimeType' => $this->detectMediaType($imagePath),
            ];
        }

        $prompt = "Analyze this document page and extract structured content.\n";

        if (trim($rawText) !== '') {
            $prompt .= "Here is the raw OCR text as a starting point:\n---\n{$rawText}\n---\n";
        }

        $prompt .= <<<'JSON_SPEC'
Respond with ONLY a JSON object (no markdown fence, no commentary) matching exactly:
{
  "title": string,             // page or section title, "" if none
  "paragraphs": string[],      // ordered, corrected paragraphs
  "tables": string[][][],      // each table = rows of string cells, [] if none
  "confidence": number         // 0.0 to 1.0
}
JSON_SPEC;

        $content[] = ['type' => 'text', 'text' => $prompt];

        $mergedOptions = $options + $this->options;
        $lastError = null;

        for ($attempt = 1; $attempt <= self::STRUCTURE_MAX_ATTEMPTS; $attempt++) {
            $answer = $this->provider->chat(self::SYSTEM_PROMPT, $content, $mergedOptions);
            $data = $this->decodeJsonAnswer($answer);

            if ($data !== null) {
                $page = PageContent::fromArray($data);

                return [
                    'title'      => $page->title,
                    'paragraphs' => $page->paragraphs,
                    'tables'     => $page->tables,
                    'confidence' => $page->confidence,
                ];
            }

            $lastError = mb_substr($answer, 0, 200);
        }

        throw LlmException::forResponse(
            'structured-output',
            "no valid JSON after " . self::STRUCTURE_MAX_ATTEMPTS . " attempts: {$lastError}",
        );
    }

    /** @return array<string, mixed>|null */
    private function decodeJsonAnswer(string $answer): ?array
    {
        $answer = trim($answer);

        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/s', $answer, $m)) {
            $answer = $m[1];
        }

        if (! str_starts_with($answer, '{')) {
            $start = strpos($answer, '{');
            $end   = strrpos($answer, '}');
            if ($start === false || $end === false || $end <= $start) {
                return null;
            }
            $answer = substr($answer, $start, $end - $start + 1);
        }

        $decoded = json_decode($answer, true);

        return is_array($decoded) ? Cast::asMap($decoded) : null;
    }

    private function detectMediaType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            'bmp'         => 'image/bmp',
            default       => 'image/png',
        };
    }
}
