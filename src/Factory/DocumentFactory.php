<?php

declare(strict_types=1);

namespace Paperdoc\Factory;

use Paperdoc\Contracts\{DocumentInterface, RendererInterface};
use Paperdoc\Document\Document;
use Paperdoc\Enum\Format;
use Paperdoc\Renderers\{CsvRenderer, DocRenderer, DocxRenderer, HtmlRenderer, MarkdownRenderer, PdfRenderer, PptRenderer, PptxRenderer, XlsRenderer, XlsxRenderer};

class DocumentFactory
{
    /** @var array<string, class-string<RendererInterface>> */
    private static array $renderers = [
        'pdf'      => PdfRenderer::class,
        'html'     => HtmlRenderer::class,
        'csv'      => CsvRenderer::class,
        'md'       => MarkdownRenderer::class,
        'markdown' => MarkdownRenderer::class,
        'docx'     => DocxRenderer::class,
        'doc'      => DocRenderer::class,
        'xlsx'     => XlsxRenderer::class,
        'xls'      => XlsRenderer::class,
        'pptx'     => PptxRenderer::class,
        'ppt'      => PptRenderer::class,
    ];

    public static function createDocument(Format|string $format, string $title = ''): DocumentInterface
    {
        return new Document(self::normalizeFormat($format), $title);
    }

    public static function normalizeFormat(Format|string $format): string
    {
        return $format instanceof Format ? $format->value : strtolower($format);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function getRenderer(Format|string $format): RendererInterface
    {
        $format = self::normalizeFormat($format);

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
