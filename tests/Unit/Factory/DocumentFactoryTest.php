<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Factory;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\{DocumentInterface, RendererInterface};
use Paperdoc\Factory\DocumentFactory;
use Paperdoc\Renderers\{CsvRenderer, HtmlRenderer, PdfRenderer};

class DocumentFactoryTest extends TestCase
{
    public function test_create_document(): void
    {
        $doc = DocumentFactory::createDocument('pdf', 'Test');

        $this->assertInstanceOf(DocumentInterface::class, $doc);
        $this->assertSame('pdf', $doc->getFormat());
        $this->assertSame('Test', $doc->getTitle());
    }

    public function test_create_document_normalizes_format(): void
    {
        $doc = DocumentFactory::createDocument('PDF');

        $this->assertSame('pdf', $doc->getFormat());
    }

    public function test_create_document_default_title(): void
    {
        $doc = DocumentFactory::createDocument('html');

        $this->assertSame('', $doc->getTitle());
    }

    public function test_get_renderer_pdf(): void
    {
        $renderer = DocumentFactory::getRenderer('pdf');

        $this->assertInstanceOf(PdfRenderer::class, $renderer);
        $this->assertSame('pdf', $renderer->getFormat());
    }

    public function test_get_renderer_html(): void
    {
        $renderer = DocumentFactory::getRenderer('html');

        $this->assertInstanceOf(HtmlRenderer::class, $renderer);
    }

    public function test_get_renderer_csv(): void
    {
        $renderer = DocumentFactory::getRenderer('csv');

        $this->assertInstanceOf(CsvRenderer::class, $renderer);
    }

    public function test_get_renderer_case_insensitive(): void
    {
        $renderer = DocumentFactory::getRenderer('PDF');

        $this->assertInstanceOf(PdfRenderer::class, $renderer);
    }

    public function test_get_renderer_unsupported_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Format non supporté');

        DocumentFactory::getRenderer('bmp');
    }

    public function test_register_renderer(): void
    {
        DocumentFactory::registerRenderer('custom', PdfRenderer::class);

        $renderer = DocumentFactory::getRenderer('custom');
        $this->assertInstanceOf(PdfRenderer::class, $renderer);
    }

    public function test_get_supported_renderer_formats(): void
    {
        $formats = DocumentFactory::getSupportedRendererFormats();

        $this->assertContains('pdf', $formats);
        $this->assertContains('html', $formats);
        $this->assertContains('csv', $formats);
    }
}
