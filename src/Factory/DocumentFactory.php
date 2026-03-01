<?php

declare(strict_types=1);

namespace Paperdoc\Factory;

use Paperdoc\Contracts\{DocumentInterface, RendererInterface};
use Paperdoc\Document\Document;
use Paperdoc\Renderers\{CsvRenderer, HtmlRenderer, MarkdownRenderer, PdfRenderer, PptxRenderer, XlsxRenderer};

class DocumentFactory
{
    /** @var array<string, class-string<RendererInterface>> */
    private static array $renderers = [
        'pdf'      => PdfRenderer::class,
        'html'     => HtmlRenderer::class,
        'csv'      => CsvRenderer::class,
        'md'       => MarkdownRenderer::class,
        'markdown' => MarkdownRenderer::class,
        'xlsx'     => XlsxRenderer::class,
        'xls'      => XlsxRenderer::class,
        'pptx'     => PptxRenderer::class,
        'ppt'      => PptxRenderer::class,
    ];

    public static function createDocument(string $format, string $title = ''): DocumentInterface
    {
        return new Document(strtolower($format), $title);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function getRenderer(string $format): RendererInterface
    {
        $format = strtolower($format);

        if (! isset(self::$renderers[$format])) {
            throw new \InvalidArgumentException("Format non supporté : {$format}");
        }

        return new (self::$renderers[$format])();
    }

    /**
     * @param class-string<RendererInterface> $rendererClass
     */
    public static function registerRenderer(string $format, string $rendererClass): void
    {
        self::$renderers[strtolower($format)] = $rendererClass;
    }

    /**
     * @return string[]
     */
    public static function getSupportedRendererFormats(): array
    {
        return array_keys(self::$renderers);
    }
}
