<?php

declare(strict_types=1);

namespace Pagina\Enum;

enum Format: string
{
    case PDF  = 'pdf';
    case HTML = 'html';
    case CSV  = 'csv';

    public function extension(): string
    {
        return $this->value;
    }

    public function mimeType(): string
    {
        return match ($this) {
            self::PDF  => 'application/pdf',
            self::HTML => 'text/html',
            self::CSV  => 'text/csv',
        };
    }

    public static function fromExtension(string $extension): self
    {
        $extension = strtolower(trim($extension, '.'));

        return match ($extension) {
            'pdf'        => self::PDF,
            'html', 'htm'=> self::HTML,
            'csv', 'tsv' => self::CSV,
            default      => throw new \InvalidArgumentException("Format non supporté : .{$extension}"),
        };
    }
}
