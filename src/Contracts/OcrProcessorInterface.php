<?php

declare(strict_types=1);

namespace Paperdoc\Contracts;

interface OcrProcessorInterface
{
    /**
     * Extract text from an image file using OCR.
     *
     * @param  string $imagePath Absolute path to the image file
     * @param  string $language  Language code (e.g. 'fra', 'eng')
     * @return string            Recognized text
     *
     * @throws \RuntimeException
     */
    public function recognize(string $imagePath, string $language = 'eng'): string;

    /**
     * Whether the OCR engine is installed and usable.
     */
    public function isAvailable(): bool;

    /**
     * Build the shell command for OCR without executing it.
     *
     * Used by ProcessPool to launch multiple OCR processes in parallel.
     *
     * @param  string $imagePath Absolute path to the image file
     * @param  string $language  Language code (e.g. 'fra', 'eng')
     * @return string            Full shell command
     */
    public function buildCommand(string $imagePath, string $language = 'eng'): string;

    /**
     * Detect the script and orientation of an image.
     *
     * @return array{script?: string, confidence?: float, orientation?: int}|null
     */
    public function detectScript(string $imagePath): ?array;
}
