# Paperdoc Library

[![Latest Version](https://img.shields.io/packagist/v/paperdoc-dev/paperdoc-lib.svg?style=flat-square)](https://packagist.org/packages/paperdoc-dev/paperdoc-lib)
[![Pre-release](https://img.shields.io/badge/stability-unstable-orange?style=flat-square)](https://github.com/paperdoc-dev/paperdoc-lib/releases)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue?style=flat-square)](https://www.php.net)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)
[![Tests](https://img.shields.io/github/actions/workflow/status/paperdoc-dev/paperdoc-lib/tests.yml?label=tests&style=flat-square)](https://github.com/paperdoc-dev/paperdoc-lib/actions)

> A zero-dependency PHP library for generating, parsing and converting documents — PDF, HTML, CSV, DOCX, XLSX, PPTX, Markdown and more.

---

## Features

- **Generate** documents from scratch (PDF, HTML, CSV, DOCX, XLSX, PPTX, Markdown)
- **Parse** existing documents into a unified in-memory model
- **Convert** between any supported formats in one call
- **Rich document model** — typed headings, ordered/bullet lists (nested), bookmarks, code blocks, blockquotes, images, tables, page breaks and typed document properties (author, subject, dates…)
- **Per-page layout** *(v0.7.0+)* — per-section `PageSetup` with custom size (or any `PageSize` enum), padding, full-page **background image** (`cover` / `contain` / `auto` / `stretch` since v0.7.1) **or color** ; absolutely-positioned **`TextZone`** blocks with `clip` / `ellipsis` / `visible` overflow strategies and **per-paragraph alignment** (`left` / `center` / `right` / `justify`, *v0.7.1*) ; document-wide running **headers / footers** with `{page}` / `{pages}` / `{title}` / `{date}` / `{datetime}` placeholders
- **Native rendering core** — every block element renders cleanly to **DOCX**, **PDF**, **HTML** and **Markdown**: typed headings (`<h1>`/`<w:pStyle>`), nested lists (`<ul>`/`<w:numPr>`), blockquotes, code blocks (with language hint), bookmarks, embedded or on-disk images
- **Hyperlinks** — parse `<w:hyperlink>` from DOCX and round-trip them to HTML `<a>`, Markdown `[text](url)` and DOCX hyperlink relationships, with anchors and tooltips
- **Batch processing** — open and process multiple files at once
- **Laravel integration** — first-class ServiceProvider and Facade
- **AI-powered** features via Neuron AI (OCR, LLM extraction)
- **Typed exceptions** — `ParserException`, `RendererException`, `UnsupportedFormatException`, `InvalidDocumentException` all extending a common `PaperdocException`
- Zero native binary dependencies — pure PHP

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | ^8.2 |
| ext-dom | * |
| ext-mbstring | * |
| ext-zip | * |
| ext-zlib | * |

**Optional (Laravel)**

| Package | Version |
|---|---|
| illuminate/support | ^11.0 \| ^12.0 |

---

## Installation

```bash
composer require paperdoc-dev/paperdoc-lib
```

### Laravel auto-discovery

The `PaperdocServiceProvider` and `Paperdoc` facade are registered automatically via Laravel's package auto-discovery.

---

## Quick Start

`DocumentManager` uses **static** methods (`create`, `save`, `open`, …). A document is built from **`Section`** instances: use `addSection($section)` or `addSection()` to append an empty section, or **`openSection()`** when you want a fluent chain (`addParagraph`, `addHeading`, …) on the new section. Bold and other run styles live on **`TextStyle`**. For advanced layouts (custom page size, full-page backgrounds, absolutely-positioned text zones, running headers/footers), see [Page layout](#page-layout-text-zones-headers--footers) below.

### Standalone PHP

```php
use Paperdoc\Support\DocumentManager;
use Paperdoc\Document\Style\TextStyle;

$doc = DocumentManager::create('pdf', 'My Report');

$doc->openSection()
    ->addParagraph('Hello, Paperdoc!', TextStyle::make()->setBold());

DocumentManager::save($doc, 'output/report.pdf');
```

### Laravel (via Facade)

```php
use Paperdoc\Facades\Paperdoc;

// Create
$doc = Paperdoc::create('docx', 'Invoice #1042');
$doc->openSection()->addParagraph('Amount due: $500');
Paperdoc::save($doc, storage_path('invoices/1042.docx'));

// Parse an existing file
$doc = Paperdoc::open('uploads/report.xlsx');

// Convert directly
Paperdoc::convert('report.docx', 'report.pdf', 'pdf');

// Render as string
$html = Paperdoc::renderAs($doc, 'html');

// Batch open
$docs = Paperdoc::openBatch([
    'file1.pdf',
    'file2.docx',
    'file3.xlsx',
]);
```

---

## Supported Formats

| Format | Parse | Render/Generate |
|--------|:-----:|:---------------:|
| PDF    | ✅    | ✅              |
| HTML   | ✅    | ✅              |
| DOCX   | ✅    | ✅              |
| XLSX   | ✅    | ✅              |
| PPTX   | ✅    | ✅              |
| CSV    | ✅    | ✅              |
| Markdown | ✅  | ✅              |
| DOC    | ✅    | ✅              |
| XLS    | ✅    | ✅              |
| PPT    | ✅    | ✅              |

---

## Document Model

Every format shares the same strongly-typed in-memory structure:

```
Document (format, title, ?Metadata, metadata[])
└── Section[]
    ├── Heading (level 1-6, runs, ?id)
    ├── Paragraph (TextRun[], ?ParagraphStyle)
    │   └── TextRun (text, ?TextStyle, ?TextLink)
    ├── ListBlock (bullet | ordered, start)
    │   └── ListItem (runs, blocks → nested ListBlock…)
    ├── Blockquote (nested DocumentElement[])
    ├── CodeBlock (code, ?language)
    ├── Bookmark (id) — link target for TextLink anchors
    ├── Table → TableRow[] → TableCell[]
    ├── Image (src | embedded data + mimeType)
    └── PageBreak
```

All block elements implement `Paperdoc\Contracts\BlockElementInterface`. Styles live in `Document/Style/` (`ParagraphStyle`, `TextStyle`, `TableStyle`), links in `Document/Link/TextLink`, typed document properties in `Document/Metadata`.

### Example — build a richly-typed document

```php
use Paperdoc\Document\{Document, Section, Metadata, ListBlock};
use Paperdoc\Document\Style\TextStyle;

$doc = Document::make('md', 'Release notes v0.5.0')
    ->setProperties(
        Metadata::make()
            ->setAuthor('Alice')
            ->setKeywords('release, changelog, paperdoc')
            ->setLanguage('en-US')
    );

$section = $doc->openSection();

$section->addElement(\Paperdoc\Document\Heading::make('Getting started', 2, 'intro'));

$section->addBulletList()
    ->addText('Install the library')
    ->addText('Run the quick start')
    ->addText('Read the docs');

$section->addCodeBlock("composer require paperdoc-dev/paperdoc-lib", 'bash');

$section->addBookmark('ready-to-go');

$section->addBlockquote()
    ->addText('You are all set.', TextStyle::make()->setItalic());
```

---

## Page layout, text zones, headers & footers

*Available since **v0.7.0**.* Each section can declare its own page
geometry through a `PageSetup` value object, and place
absolutely-positioned `TextZone` blocks anywhere on the page. Combine
several sections (each with its own `PageSetup`) to build documents
where every page has a different size and background. Add a global
`RunningElement` to the `Document` to draw a header/footer on every
page.

### Configure a page

```php
use Paperdoc\Document\{Image, Section};
use Paperdoc\Document\Style\PageSetup;
use Paperdoc\Enum\PageSize;

$cover = Section::make('cover')->setPageSetup(
    PageSetup::fromSize(PageSize::A4)
        ->setPadding(0)                                // 1, 2, 3 or 4 values (CSS shorthand)
        ->setBackgroundImage(Image::make('cover.jpg')) // full-bleed image
);

$body = Section::make('body')->setPageSetup(
    PageSetup::fromSize(PageSize::A4, PageSetup::ORIENTATION_LANDSCAPE)
        ->setPadding(50)
        ->setBackgroundColor('#F8F5EC')                // solid color
);

$square = Section::make('back-cover')->setPageSetup(
    PageSetup::custom(500, 500)                        // any width × height in pt
        ->setBackgroundImage(Image::make('back.jpg'))
);
```

`Section` exposes shortcut setters (`setPageSize()`,
`setPageDimensions()`, `setPagePadding()`,
`setPageBackgroundImage()`, `setPageBackgroundColor()`) that delegate
to a lazily-created `PageSetup`.

| Setter / Factory                                            | Purpose                                                |
|-------------------------------------------------------------|--------------------------------------------------------|
| `PageSetup::fromSize(PageSize, $orientation = 'portrait')`  | Use a standard format (A3/A4/A5/A6/Letter/Legal/Tabloid/Executive) |
| `PageSetup::custom($width, $height)`                        | Any dimensions in PDF points                           |
| `landscape()` / `portrait()`                                | Flip the active orientation                            |
| `setPadding(...)` (1–4 values)                              | CSS-style shorthand for top/right/bottom/left padding  |
| `setBackgroundColor($hex)`                                  | Solid full-bleed background color                      |
| `setBackgroundImage(Image)`                                 | Full-bleed image (on-disk or `Image::fromData()`)      |
| `setBackgroundSize(string)` *(v0.7.1)*                      | `cover` (default), `contain`, `auto`, `stretch` (=`100% 100%`), or any CSS string |
| `setBackgroundPosition(string)` *(v0.7.1)*                  | CSS string, default `'center center'`                  |
| `setBackgroundRepeat(string)` *(v0.7.1)*                    | CSS string, default `'no-repeat'`                      |

### Fit the background image — `cover` / `contain` / `auto` / `stretch`

*Available since **v0.7.1**.* Both renderers (PDF and HTML) honour the
same four CSS-like modes. `cover` and `auto` automatically clip the
overflow with a clip path in the PDF and `overflow: hidden` in the HTML
output.

```php
use Paperdoc\Document\Style\PageSetup;

$page->setPageSetup(
    PageSetup::fromSize(PageSize::A4)
        ->setBackgroundImage(Image::make('hero.jpg'))
        ->setBackgroundSize(PageSetup::BG_SIZE_COVER)   // default
);
```

| `BG_SIZE_*` constant | CSS equivalent | Behaviour                                                                     |
|----------------------|----------------|-------------------------------------------------------------------------------|
| `BG_SIZE_COVER`      | `cover`        | Fills the page, preserves aspect ratio, **overflow is clipped** (default)     |
| `BG_SIZE_CONTAIN`    | `contain`      | Fits inside the page, preserves aspect ratio (may leave empty bands)          |
| `BG_SIZE_AUTO`       | `auto`         | Image at its natural size, centred, clipped if larger than the page           |
| `BG_SIZE_STRETCH`    | `100% 100%`    | Stretches to fill the page; aspect ratio is **not** preserved (legacy mode)   |

Any other CSS-valid string (`'50% auto'`, `'300pt 200pt'`, …) is
accepted as-is in HTML output.

### Place text precisely with `TextZone`

```php
use Paperdoc\Document\TextZone;
use Paperdoc\Document\Style\{ParagraphStyle, TextStyle};
use Paperdoc\Enum\Alignment;

$cover->addTextZone(x: 40, y: 40, width: 515, height: 90)
    ->setBackgroundColor('#0B1437')
    ->setBorder('#FFFFFF', 0.8)
    ->setPadding(16)
    ->addText(
        'Paperdoc — Cover title',
        TextStyle::make()->setBold()->setFontSize(20)->setColor('#FFFFFF'),
        ParagraphStyle::make()->setAlignment(Alignment::LEFT),
    );

// Long lorem with the ellipsis strategy: text is truncated to fit
// exactly the visible height and the last visible line ends with "…".
$cover->addTextZone(x: 40, y: 160, width: 250, height: 260)
    ->setPadding(12)
    ->setBackgroundColor('#FFFFFF')
    ->setBorder('#1F2937', 0.5)
    ->setOverflow(TextZone::OVERFLOW_ELLIPSIS)
    ->addText($veryLongText,
        TextStyle::make()->setFontSize(10)->setColor('#111827'),
        ParagraphStyle::make()->setLineSpacing(1.25),
    );
```

| Overflow strategy           | Behaviour                                                                                          |
|-----------------------------|----------------------------------------------------------------------------------------------------|
| `TextZone::OVERFLOW_CLIP`     | (Default) Silently truncates content that doesn't fit                                            |
| `TextZone::OVERFLOW_ELLIPSIS` | Truncates and ends the last visible line with `…` (PDF: native; HTML: pseudo-element)            |
| `TextZone::OVERFLOW_VISIBLE`  | No clipping — content may flow outside the box (kept for parity with CSS)                        |

Coordinates use the top-left convention (`x=0, y=0` is the top-left
of the page) for both PDF and HTML — the `PdfRenderer` flips to PDF's
bottom-left origin internally.

#### Per-paragraph alignment inside a zone — *v0.7.1*

Each paragraph of a `TextZone` carries its own `ParagraphStyle`, so
you can mix several alignments in the same zone (centred title,
justified body, right-aligned signature, …):

```php
use Paperdoc\Enum\Alignment;

$zone = $page->addTextZone(40, 80, 515, 380)
    ->setBackgroundColor('#FFFFFF')
    ->setOverflow(TextZone::OVERFLOW_ELLIPSIS);

$zone->addText('Quarterly report',
    TextStyle::make()->setBold()->setFontSize(18),
    ParagraphStyle::make()->setAlignment(Alignment::CENTER));

$zone->addText($longLorem,
    TextStyle::make()->setFontSize(11),
    ParagraphStyle::make()->setAlignment(Alignment::JUSTIFY)->setLineSpacing(1.3));

$zone->addText('— J. Doe',
    TextStyle::make()->setItalic(),
    ParagraphStyle::make()->setAlignment(Alignment::RIGHT));
```

In the PDF, justification is implemented with the native PDF
word-spacing operator (`Tw`); the last line of a paragraph is
intentionally left-aligned to avoid stretched short lines.

### Document-wide headers and footers

```php
use Paperdoc\Document\Style\{RunningElement, TextStyle};
use Paperdoc\Enum\Alignment;
use Paperdoc\Support\DocumentManager;

$doc = DocumentManager::create('pdf', 'Quarterly report');

$doc->setHeader(
    RunningElement::make('{title}')
        ->setAlignment(Alignment::LEFT)
        ->setStyle(TextStyle::make()->setFontSize(9)->setItalic()->setColor('#FFFFFF'))
);

$doc->setFooter(
    RunningElement::make('Page {page} / {pages}  ·  {date}')
        ->setAlignment(Alignment::CENTER)
        ->setStyle(TextStyle::make()->setFontSize(9)->setColor('#FFFFFF'))
);
```

Supported placeholders in the template: `{page}` (1-indexed current
page), `{pages}` (total pages), `{title}` (the document title),
`{date}` (`Y-m-d`) and `{datetime}` (`Y-m-d H:i`). The renderer
resolves them per page so you don't need to update the template
between pages.

The HTML renderer adds a translucent `rgba(255, 255, 255, 0.85)`
backdrop with a `backdrop-filter: blur(2px)` behind the running
elements so they remain legible on top of any background image. The
library does not automatically reserve vertical space for the
header/footer — keep that in mind when positioning a `TextZone` close
to a page edge.

---

## Rendering

Since **v0.5.0**, every element of the document model is natively rendered by **all four** core renderers — no element is silently dropped, every output is a valid file format.

| Element            | DOCX                                                    | PDF                                            | HTML                                  | Markdown                          |
|--------------------|---------------------------------------------------------|------------------------------------------------|---------------------------------------|-----------------------------------|
| `Heading` (1–6)    | `<w:pStyle w:val="HeadingN"/>` + bookmark anchor        | typed font sizes (24/20/16/14/13/12 pt) + navy | `<h1>`…`<h6>` with `id`               | `#`…`######`, optional `{#id}`    |
| `Paragraph`        | `<w:p>` + run styling                                   | wrapped text + inline run styles               | `<p>` + inline `<span>`               | plain text + emphasis             |
| `ListBlock`        | `<w:numPr>` + `word/numbering.xml`, nested `<w:ilvl>`   | `•` / `1.` markers, depth-based indent         | `<ul>` / `<ol start="N">`, nested     | `-` / `1.`, two-space indent      |
| `Blockquote`       | `<w:pStyle w:val="Quote"/>` + indent                    | indented italic muted-grey                     | `<blockquote>` (nested children)      | `> ` prefixed lines               |
| `CodeBlock`        | `<w:pStyle w:val="Code"/>` + Consolas + `<w:br/>`       | Courier, dedicated spacing                     | `<pre><code class="language-…">`      | fenced ` ```lang ` block          |
| `Bookmark`         | `<w:bookmarkStart/>` / `<w:bookmarkEnd/>`               | rendered silently (PDF annotations: roadmap)   | `<a id="…" class="paperdoc-bookmark">`| inline `<a id="…"></a>`           |
| `TextLink`         | `<w:hyperlink>` (external rels + `w:anchor` + tooltip)  | blue underlined run                            | `<a href>` with safe `target/rel`     | safe `[label](url "title")`       |
| `Image`            | `<w:drawing>` + `word/media/imageN.ext` rel             | XObject DCT (JPEG/PNG/GIF via GD re-encode)    | `<img src>` or `data:` URI            | `![alt](path)` or `data:` URI     |
| `Table`            | `<w:tbl>` with header rows + `gridSpan`                 | drawn cells with header bg                     | `<table>` + striped rows              | `\|` rows                         |
| `PageBreak`        | `<w:br w:type="page"/>`                                 | `newPage()`                                    | `.page-break` divider                 | blank line                        |
| `Metadata`         | `docProps/core.xml`                                     | PDF `/Creator`                                 | (HTML head meta — roadmap)            | (frontmatter — roadmap)           |

Both `Image::make($path)` (on-disk) and `Image::fromData($bytes, $mimeType)` (in-memory) are accepted everywhere; HTML and Markdown automatically inline embedded images as `data:` URIs, DOCX writes them to `word/media/`, and PDF embeds them as DCT XObjects (re-encoding GIF/PNG/WebP through GD when needed).

---

## Typed Exceptions

All library errors extend a single base so consumers can catch them uniformly:

| Exception | Thrown when… |
|---|---|
| `Paperdoc\Exceptions\PaperdocException` | Base (extends `RuntimeException`) |
| `Paperdoc\Exceptions\ParserException` | A parser cannot read/decode a file (`::forFile($path, $reason, $previous)`) |
| `Paperdoc\Exceptions\RendererException` | A renderer cannot serialise a document (`::forFormat($fmt, $reason, $previous)`) |
| `Paperdoc\Exceptions\UnsupportedFormatException` | Unknown format or extension (`::forFormat()` / `::forExtension()`) |
| `Paperdoc\Exceptions\InvalidDocumentException` | Document is used in an invalid state (e.g. invalid heading level) |

```php
use Paperdoc\Exceptions\PaperdocException;

try {
    $doc = Paperdoc::open('report.docx');
} catch (PaperdocException $e) {
    // Any Paperdoc error ends up here.
}
```

---

## Hyperlinks

Every `TextRun` can carry an optional `Paperdoc\Document\Link\TextLink`. Links survive the full round-trip: they're parsed from DOCX (`<w:hyperlink>`) and rendered natively by the HTML and Markdown renderers.

### Add a link programmatically

```php
use Paperdoc\Support\DocumentManager;
use Paperdoc\Document\Section;
use Paperdoc\Document\Link\TextLink;

$doc = DocumentManager::create('md', 'Release notes');
$section = Section::make('main');

$section->addText(
    'See the full changelog',
    null,
    TextLink::make('https://github.com/paperdoc-dev/paperdoc-lib/blob/main/CHANGELOG.md', '', 'Changelog')
);

$doc->addSection($section);
echo DocumentManager::renderAs($doc, 'md');
// [See the full changelog](https://github.com/paperdoc-dev/paperdoc-lib/blob/main/CHANGELOG.md "Changelog")
```

### Supported link flavours

| Kind             | Construction                                         | HTML output                                             | Markdown output          |
|------------------|------------------------------------------------------|---------------------------------------------------------|--------------------------|
| External URL     | `TextLink::make('https://x.com')`                    | `<a href="…" target="_blank" rel="noopener noreferrer">…</a>` | `[label](url)`     |
| Internal anchor  | `TextLink::make('', 'section-2')`                    | `<a href="#section-2">…</a>`                             | `[label](#section-2)`   |
| URL + fragment   | `TextLink::make('https://x.com', 'sect-2')`          | `<a href="https://x.com#sect-2" …>…</a>`                 | `[label](url#sect-2)`   |
| Tooltip / title  | `TextLink::make('https://x.com', '', 'Open site')`   | `<a … title="Open site" …>…</a>`                         | `[label](url "Open site")` |

External schemes (`http`, `https`, `mailto`, `tel`, `ftp`) automatically get `target="_blank" rel="noopener noreferrer"` in HTML to prevent tabnabbing. Run styling (bold, italic, color, font) is preserved when combined with a link.

### Convert DOCX with hyperlinks to Markdown

```php
use Paperdoc\Support\DocumentManager;

// <w:hyperlink r:id="…"> elements are parsed and attached to their TextRun
$doc = DocumentManager::open('report.docx');

// Links are rendered as safe [label](url) — labels with ] and URLs with spaces
// or parentheses are escaped/wrapped automatically.
file_put_contents('report.md', DocumentManager::renderAs($doc, 'md'));
```

---

## Configuration

Publish the config (Laravel):

```bash
php artisan vendor:publish --tag=paperdoc-config
```

This creates `config/paperdoc.php` where you can set the default format, text styles, storage paths, and AI/OCR settings.

---

## Testing

```bash
composer test
# or
./vendor/bin/phpunit
```

Integration tests live in `tests/Integration/`, unit tests in `tests/Unit/`.

---

## Architecture

```
src/
├── Concerns/          # Shared traits
├── Console/           # Artisan commands
├── Contracts/         # DocumentInterface, ParserInterface, BlockElementInterface…
├── Document/          # Core model (Document, Section, Paragraph, Heading, ListBlock, Bookmark, CodeBlock, Blockquote, Metadata…)
├── Enum/              # Format enums
├── Exceptions/        # PaperdocException + typed exceptions
├── Facades/           # Laravel Facade
├── Factory/           # Document/Parser factories
├── Llm/               # AI/LLM integration (Neuron AI)
├── Ocr/               # OCR integration
├── Parsers/           # Format-specific parsers
├── Renderers/         # Format-specific renderers
├── Support/           # DocumentManager and helpers
└── PaperdocServiceProvider.php
```

---

## Contributing

We welcome contributions! Please read [CONTRIBUTING.md](CONTRIBUTING.md) before opening a pull request.

### Contributors

Thanks to everyone who has contributed to **paperdoc-lib**. A full list is kept in [CONTRIBUTORS.md](CONTRIBUTORS.md).

- **Olivier Mourlevat** — [@olivM](https://github.com/olivM) — DOCX hyperlink parsing, HTML/Markdown hyperlink rendering ([#4](https://github.com/paperdoc-dev/paperdoc-lib/pull/4))

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for release history.

---

## License

Paperdoc Library is released under the **[MIT License](LICENSE)** — free to use, modify and distribute, commercial or not.

© Paperdoc — [paperdoc.dev](https://paperdoc.dev)
