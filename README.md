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
- **Hyperlinks** — parse `<w:hyperlink>` from DOCX and round-trip them to HTML `<a>` or Markdown `[text](url)`, with anchors and tooltips
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

`DocumentManager` uses **static** methods (`create`, `save`, `open`, …). A document is built from **`Section`** instances: use `addSection($section)` or `addSection()` to append an empty section, or **`openSection()`** when you want a fluent chain (`addParagraph`, `addHeading`, …) on the new section. Bold and other run styles live on **`TextStyle`** (see `examples/usage.php` for tables and advanced composition).

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

$doc = Document::make('md', 'Release notes v0.4.0')
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
