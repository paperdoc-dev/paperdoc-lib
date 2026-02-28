<?php

declare(strict_types=1);

namespace Pagina\Facades;

use Illuminate\Support\Facades\Facade;
use Pagina\Contracts\DocumentInterface;
use Pagina\Support\DocumentManager;

/**
 * @method static DocumentInterface create(string $format, string $title = '')
 * @method static DocumentInterface open(string $filename)
 * @method static void save(DocumentInterface $document, string $filename, ?string $format = null)
 * @method static void convert(string $sourceFile, string $targetFile, string $targetFormat)
 * @method static void registerWriter(string $format, string $writerClass)
 * @method static void registerParser(\Pagina\Contracts\ParserInterface $parser)
 *
 * @see DocumentManager
 */
class Pagina extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DocumentManager::class;
    }
}
