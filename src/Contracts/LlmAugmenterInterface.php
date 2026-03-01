<?php

declare(strict_types=1);

namespace Paperdoc\Contracts;

interface LlmAugmenterInterface
{
    /**
     * Correct and clean raw OCR text using a language model.
     *
     * @param  string               $rawText  Raw OCR output
     * @param  array<string, mixed> $options  Provider-specific options
     * @return string                         Cleaned text
     */
    public function enhance(string $rawText, array $options = []): string;

    /**
     * Extract structured document content (headings, paragraphs, tables)
     * from raw text and/or an image using a language model.
     *
     * @param  string               $rawText   Raw OCR output (may be empty)
     * @param  string|null          $imagePath Path to the page image for vision models
     * @param  array<string, mixed> $options   Provider-specific options
     * @return array{title: string, paragraphs: string[], tables: array<int, string[][]>, confidence: float}
     */
    public function structureDocument(string $rawText, ?string $imagePath = null, array $options = []): array;
}
