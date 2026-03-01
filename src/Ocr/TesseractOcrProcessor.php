<?php

declare(strict_types=1);

namespace Paperdoc\Ocr;

use Paperdoc\Contracts\OcrProcessorInterface;

class TesseractOcrProcessor implements OcrProcessorInterface
{
    private string $binary;

    /** @var string[] */
    private array $extraOptions;

    /**
     * @param string   $binary       Path to the tesseract binary
     * @param string[] $extraOptions Additional CLI flags (e.g. ['--psm 3'])
     */
    public function __construct(string $binary = 'tesseract', array $extraOptions = [])
    {
        $this->binary = $binary;
        $this->extraOptions = $extraOptions;
    }

    public function buildCommand(string $imagePath, string $language = 'eng'): string
    {
        return sprintf(
            '%s %s stdout -l %s %s 2>/dev/null',
            escapeshellcmd($this->binary),
            escapeshellarg($imagePath),
            escapeshellarg($language),
            implode(' ', $this->extraOptions),
        );
    }

    public function recognize(string $imagePath, string $language = 'eng'): string
    {
        if (! file_exists($imagePath) || ! is_readable($imagePath)) {
            throw new \RuntimeException("Image introuvable ou illisible : {$imagePath}");
        }

        if (! $this->isAvailable()) {
            throw new \RuntimeException(
                "Tesseract n'est pas installé ou introuvable. "
                . "Installez-le via : apt install tesseract-ocr tesseract-ocr-{$language}"
            );
        }

        $cmd = $this->buildCommand($imagePath, $language);

        $output = [];
        $exitCode = 0;
        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            throw new \RuntimeException(
                "Tesseract a échoué (code {$exitCode}) : " . implode("\n", $output)
            );
        }

        $lines = array_filter($output, fn (string $line) =>
            trim($line) !== ''
            && ! str_starts_with($line, 'Corrupt JPEG data')
            && ! str_starts_with($line, 'Warning:')
        );

        return trim(implode("\n", $lines));
    }

    public function isAvailable(): bool
    {
        $cmd = sprintf('command -v %s 2>/dev/null', escapeshellarg($this->binary));
        exec($cmd, $output, $exitCode);

        return $exitCode === 0;
    }

    public function detectScript(string $imagePath): ?array
    {
        if (! file_exists($imagePath) || ! $this->isAvailable()) {
            return null;
        }

        $cmd = sprintf(
            '%s %s - --psm 0 2>/dev/null',
            escapeshellcmd($this->binary),
            escapeshellarg($imagePath),
        );

        $output = [];
        $exitCode = 0;
        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            return null;
        }

        $result = [];
        foreach ($output as $line) {
            if (preg_match('/Script:\s*(.+)/i', $line, $m)) {
                $result['script'] = trim($m[1]);
            }
            if (preg_match('/Script confidence:\s*([\d.]+)/i', $line, $m)) {
                $result['confidence'] = (float) $m[1];
            }
            if (preg_match('/Orientation in degrees:\s*(\d+)/i', $line, $m)) {
                $result['orientation'] = (int) $m[1];
            }
        }

        return $result ?: null;
    }
}
