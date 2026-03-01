<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use Paperdoc\Enum\Format;
use Paperdoc\Enum\Alignment;
use Paperdoc\Enum\BorderStyle;

class EnumTest extends TestCase
{
    /* -------------------------------------------------------------
     | Format
     |------------------------------------------------------------- */

    public function test_format_values(): void
    {
        $this->assertSame('pdf', Format::PDF->value);
        $this->assertSame('html', Format::HTML->value);
        $this->assertSame('csv', Format::CSV->value);
        $this->assertSame('docx', Format::DOCX->value);
        $this->assertSame('doc', Format::DOC->value);
        $this->assertSame('md', Format::MD->value);
    }

    public function test_format_extension(): void
    {
        $this->assertSame('pdf', Format::PDF->extension());
        $this->assertSame('html', Format::HTML->extension());
        $this->assertSame('csv', Format::CSV->extension());
        $this->assertSame('docx', Format::DOCX->extension());
        $this->assertSame('doc', Format::DOC->extension());
        $this->assertSame('md', Format::MD->extension());
    }

    public function test_format_mime_type(): void
    {
        $this->assertSame('application/pdf', Format::PDF->mimeType());
        $this->assertSame('text/html', Format::HTML->mimeType());
        $this->assertSame('text/csv', Format::CSV->mimeType());
        $this->assertSame('application/vnd.openxmlformats-officedocument.wordprocessingml.document', Format::DOCX->mimeType());
        $this->assertSame('application/msword', Format::DOC->mimeType());
        $this->assertSame('text/markdown', Format::MD->mimeType());
    }

    public function test_format_from_extension_pdf(): void
    {
        $this->assertSame(Format::PDF, Format::fromExtension('pdf'));
        $this->assertSame(Format::PDF, Format::fromExtension('PDF'));
        $this->assertSame(Format::PDF, Format::fromExtension('.pdf'));
    }

    public function test_format_from_extension_html(): void
    {
        $this->assertSame(Format::HTML, Format::fromExtension('html'));
        $this->assertSame(Format::HTML, Format::fromExtension('htm'));
    }

    public function test_format_from_extension_csv(): void
    {
        $this->assertSame(Format::CSV, Format::fromExtension('csv'));
        $this->assertSame(Format::CSV, Format::fromExtension('tsv'));
    }

    public function test_format_from_extension_docx(): void
    {
        $this->assertSame(Format::DOCX, Format::fromExtension('docx'));
    }

    public function test_format_from_extension_doc(): void
    {
        $this->assertSame(Format::DOC, Format::fromExtension('doc'));
    }

    public function test_format_from_extension_md(): void
    {
        $this->assertSame(Format::MD, Format::fromExtension('md'));
        $this->assertSame(Format::MD, Format::fromExtension('markdown'));
        $this->assertSame(Format::MD, Format::fromExtension('mkd'));
        $this->assertSame(Format::MD, Format::fromExtension('mdown'));
    }

    public function test_format_from_extension_unsupported(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Format::fromExtension('xyz');
    }

    /* -------------------------------------------------------------
     | Alignment
     |------------------------------------------------------------- */

    public function test_alignment_values(): void
    {
        $this->assertSame('left', Alignment::LEFT->value);
        $this->assertSame('center', Alignment::CENTER->value);
        $this->assertSame('right', Alignment::RIGHT->value);
        $this->assertSame('justify', Alignment::JUSTIFY->value);
    }

    public function test_alignment_try_from(): void
    {
        $this->assertSame(Alignment::CENTER, Alignment::tryFrom('center'));
        $this->assertNull(Alignment::tryFrom('invalid'));
    }

    /* -------------------------------------------------------------
     | BorderStyle
     |------------------------------------------------------------- */

    public function test_border_style_values(): void
    {
        $this->assertSame('none', BorderStyle::NONE->value);
        $this->assertSame('solid', BorderStyle::SOLID->value);
        $this->assertSame('dashed', BorderStyle::DASHED->value);
        $this->assertSame('dotted', BorderStyle::DOTTED->value);
    }
}
