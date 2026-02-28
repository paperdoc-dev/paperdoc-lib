<?php

declare(strict_types=1);

namespace Pagina\Factory;

use Pagina\Contracts\ParserInterface;
use Pagina\Parsers\{CsvParser, HtmlParser};

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
            foreach (['html', 'htm', 'csv', 'tsv'] as $ext) {
                if ($parser->supports($ext)) {
                    $extensions[] = $ext;
                }
            }
        }

        return array_unique($extensions);
    }
}
