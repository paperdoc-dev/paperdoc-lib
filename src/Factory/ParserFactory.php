<?php

declare(strict_types=1);

namespace Paperdoc\Factory;

use Paperdoc\Contracts\ParserInterface;
use Paperdoc\Parsers\{CsvParser, DocParser, DocxParser, HtmlParser, MarkdownParser, PdfParser, PptParser, PptxParser, XlsParser, XlsxParser};

class ParserFactory
{
    /** @var ParserInterface[] */
    private static array $parsers = [];

    /** @return ParserInterface[] */
    private static function getParsers(): array
    {
        if (empty(self::$parsers)) {
            self::$parsers = [
                new HtmlParser(),
                new CsvParser(),
                new DocxParser(),
                new DocParser(),
                new PdfParser(),
                new MarkdownParser(),
                new XlsxParser(),
                new XlsParser(),
                new PptxParser(),
                new PptParser(),
            ];
        }

        return self::$parsers;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function getParser(string $filename): ParserInterface
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        foreach (self::getParsers() as $parser) {
            if ($parser->supports($extension)) {
                return $parser;
            }
        }

        throw new \InvalidArgumentException("Aucun parser disponible pour l'extension : .{$extension}");
    }

    public static function registerParser(ParserInterface $parser): void
    {
        self::$parsers[] = $parser;
    }

    /**
     * @return string[]
     */
    public static function getSupportedExtensions(): array
    {
        $extensions = [];

        foreach (self::getParsers() as $parser) {
            foreach (['html', 'htm', 'csv', 'tsv', 'docx', 'doc', 'pdf', 'md', 'markdown', 'xlsx', 'xls', 'pptx', 'ppt'] as $ext) {
                if ($parser->supports($ext)) {
                    $extensions[] = $ext;
                }
            }
        }

        return array_unique($extensions);
    }
}
