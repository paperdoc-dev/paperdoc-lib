<?php

declare(strict_types=1);

namespace Pagina\Support;

use Pagina\Contracts\DocumentInterface;
use Pagina\Factory\{DocumentFactory, ParserFactory};

/**
 * Point d'entrée principal de Pagina.
 *
 * API unifiée : create(), open(), save(), convert().
 */
class DocumentManager
{
    /* -------------------------------------------------------------
     | Create
     |------------------------------------------------------------- */

    /**
     * Crée un nouveau document vierge.
     *
     * @example $doc = DocumentManager::create('pdf');
     */
    public static function create(string $format, string $title = ''): DocumentInterface
    {
        return DocumentFactory::createDocument($format, $title);
    }

    /* -------------------------------------------------------------
     | Open / Parse
     |------------------------------------------------------------- */

    /**
     * Ouvre et parse un document existant.
     *
     * @example $doc = DocumentManager::open('data.csv');
     */
    public static function open(string $filename): DocumentInterface
    {
        $parser = ParserFactory::getParser($filename);

        return $parser->parse($filename);
    }

    /* -------------------------------------------------------------
     | Save / Write
     |------------------------------------------------------------- */

    /**
     * Sauvegarde un document dans le format spécifié.
     *
     * @param string|null $format Si null, utilise $document->getFormat()
     *
     * @example DocumentManager::save($doc, 'output.pdf', 'pdf');
     */
    public static function save(
        DocumentInterface $document,
        string $filename,
        ?string $format = null,
    ): void {
        $format ??= $document->getFormat();
        $writer = DocumentFactory::getWriter($format);
        $writer->write($document, $filename);
    }

    /* -------------------------------------------------------------
     | Convert
     |------------------------------------------------------------- */

    /**
     * Raccourci : ouvre + convertit + sauvegarde.
     *
     * @example DocumentManager::convert('data.csv', 'output.pdf', 'pdf');
     */
    public static function convert(
        string $sourceFile,
        string $targetFile,
        string $targetFormat,
    ): void {
        $document = self::open($sourceFile);
        self::save($document, $targetFile, $targetFormat);
    }

    /* -------------------------------------------------------------
     | Writer/Parser Registration
     |------------------------------------------------------------- */

    /**
     * @param class-string<\Pagina\Contracts\WriterInterface> $writerClass
     */
    public static function registerWriter(string $format, string $writerClass): void
    {
        DocumentFactory::registerWriter($format, $writerClass);
    }

    public static function registerParser(\Pagina\Contracts\ParserInterface $parser): void
    {
        ParserFactory::registerParser($parser);
    }
}
