<?php

declare(strict_types=1);

namespace Pagina\Factory;

use Pagina\Contracts\{DocumentInterface, WriterInterface};
use Pagina\Document\Document;
use Pagina\Writers\{CsvWriter, HtmlWriter, PdfWriter};

class DocumentFactory
{
    /** @var array<string, class-string<WriterInterface>> */
    private static array $writers = [
        'pdf'  => PdfWriter::class,
        'html' => HtmlWriter::class,
        'csv'  => CsvWriter::class,
    ];

    public static function createDocument(string $format, string $title = ''): DocumentInterface
    {
        return new Document(strtolower($format), $title);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function getWriter(string $format): WriterInterface
    {
        $format = strtolower($format);

        if (! isset(self::$writers[$format])) {
            throw new \InvalidArgumentException("Format non supporté : {$format}");
        }

        return new (self::$writers[$format])();
    }

    /**
     * @param class-string<WriterInterface> $writerClass
     */
    public static function registerWriter(string $format, string $writerClass): void
    {
        self::$writers[strtolower($format)] = $writerClass;
    }

    /**
     * @return string[]
     */
    public static function getSupportedWriterFormats(): array
    {
        return array_keys(self::$writers);
    }
}
