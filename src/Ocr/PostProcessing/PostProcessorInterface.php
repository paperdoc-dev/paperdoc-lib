<?php

declare(strict_types=1);

namespace Paperdoc\Ocr\PostProcessing;

interface PostProcessorInterface
{
    /**
     * Process OCR text and return the improved version.
     *
     * @param  string $text    Raw or partially-processed OCR text
     * @param  array  $context Shared context (language, entities found, etc.)
     * @return string          Improved text
     */
    public function process(string $text, array &$context): string;

    public function getName(): string;
}
