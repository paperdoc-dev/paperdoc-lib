<?php

declare(strict_types=1);

namespace Paperdoc\Enum;

enum Format: string
{
    case PDF  = 'pdf';
    case HTML = 'html';
    case CSV  = 'csv';
    case DOCX = 'docx';
    case DOC  = 'doc';
    case MD   = 'md';
    case XLSX = 'xlsx';
    case XLS  = 'xls';
    case PPTX = 'pptx';
    case PPT  = 'ppt';
    case ODT  = 'odt';
    case ODS  = 'ods';
    case ODP  = 'odp';
    case RTF  = 'rtf';
    case TXT  = 'txt';

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
            self::DOCX => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            self::DOC  => 'application/msword',
            self::MD   => 'text/markdown',
            self::XLSX => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            self::XLS  => 'application/vnd.ms-excel',
            self::PPTX => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            self::PPT  => 'application/vnd.ms-powerpoint',
            self::ODT  => 'application/vnd.oasis.opendocument.text',
            self::ODS  => 'application/vnd.oasis.opendocument.spreadsheet',
            self::ODP  => 'application/vnd.oasis.opendocument.presentation',
            self::RTF  => 'application/rtf',
            self::TXT  => 'text/plain',
        };
    }

    public function isOfficeDocument(): bool
    {
        return in_array($this, [
            self::DOC, self::DOCX,
            self::XLS, self::XLSX,
            self::PPT, self::PPTX,
            self::ODT, self::ODS, self::ODP,
            self::RTF,
        ], true);
    }

    public function isTextBased(): bool
    {
        return in_array($this, [self::HTML, self::CSV, self::MD, self::TXT], true);
    }

    public static function fromExtension(string $extension): self
    {
        $extension = strtolower(trim($extension, '.'));

        return match ($extension) {
            'pdf'                              => self::PDF,
            'html', 'htm'                      => self::HTML,
            'csv', 'tsv'                       => self::CSV,
            'docx'                             => self::DOCX,
            'doc'                              => self::DOC,
            'md', 'markdown', 'mkd', 'mdown'  => self::MD,
            'xlsx'                             => self::XLSX,
            'xls'                              => self::XLS,
            'pptx'                             => self::PPTX,
            'ppt'                              => self::PPT,
            'odt'                              => self::ODT,
            'ods'                              => self::ODS,
            'odp'                              => self::ODP,
            'rtf'                              => self::RTF,
            'txt'                              => self::TXT,
            default                            => throw new \InvalidArgumentException("Format non supporté : .{$extension}"),
        };
    }
}
