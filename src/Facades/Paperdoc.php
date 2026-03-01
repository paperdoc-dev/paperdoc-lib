<?php

declare(strict_types=1);

namespace Paperdoc\Facades;

use Illuminate\Support\Facades\Facade;
use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Support\DocumentManager;

/**
 * @method static DocumentInterface create(string $format, string $title = '')
 * @method static DocumentInterface open(string $filename, array $options = [])
 * @method static DocumentInterface[] openBatch(array $filenames, array $options = [])
 * @method static void save(DocumentInterface $document, string $filename, ?string $format = null)
 * @method static string renderAs(DocumentInterface $document, string $format)
 * @method static void convert(string $sourceFile, string $targetFile, string $targetFormat)
 * @method static void registerRenderer(string $format, string $rendererClass)
 * @method static void registerParser(\Paperdoc\Contracts\ParserInterface $parser)
 *
 * @see DocumentManager
 */
class Paperdoc extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DocumentManager::class;
    }
}
