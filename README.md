# Paperdoc Library

[![Latest Version](https://img.shields.io/packagist/v/paperdoc-dev/paperdoc-lib.svg?style=flat-square)](https://packagist.org/packages/paperdoc-dev/paperdoc-lib)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue?style=flat-square)](https://www.php.net)
[![License](https://img.shields.io/badge/license-proprietary-red?style=flat-square)](LICENSE)
[![Tests](https://img.shields.io/github/actions/workflow/status/paperdoc-dev/paperdoc-lib/tests.yml?label=tests&style=flat-square)](https://github.com/paperdoc-dev/paperdoc-lib/actions)

> A zero-dependency PHP library for generating, parsing and converting documents — PDF, HTML, CSV, DOCX, XLSX, PPTX, Markdown and more.

---

## Features

- **Generate** documents from scratch (PDF, HTML, CSV, DOCX, XLSX, PPTX, Markdown)
- **Parse** existing documents into a unified in-memory model
- **Convert** between any supported formats in one call
- **Batch processing** — open and process multiple files at once
- **Laravel integration** — first-class ServiceProvider and Facade
- **AI-powered** features via Neuron AI (OCR, LLM extraction)
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

### Standalone PHP

```php
use Paperdoc\Support\DocumentManager;

$manager = new DocumentManager();

// Create a PDF document
$doc = $manager->create('pdf', 'My Report');
$doc->addSection()
    ->addParagraph('Hello, Paperdoc!')
    ->setBold(true);

$manager->save($doc, 'output/report.pdf');
```

### Laravel (via Facade)

```php
use Paperdoc\Facades\Paperdoc;

// Create
$doc = Paperdoc::create('docx', 'Invoice #1042');
$doc->addSection()->addParagraph('Amount due: $500');
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
| DOC    | ✅    | —               |
| XLS    | ✅    | —               |
| PPT    | ✅    | —               |

---

## Document Model

Every format shares the same in-memory structure:

```
Document
└── Section[]
    ├── Paragraph (with TextRun[], bold, italic, font…)
    ├── Table → TableRow[] → TableCell[]
    ├── Image
    └── PageBreak
```

Styles are encapsulated in `Document/Style/` and can be applied at the paragraph, run, or section level.

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
├── Contracts/         # DocumentInterface, ParserInterface…
├── Document/          # Core model (Document, Section, Paragraph…)
├── Enum/              # Format enums
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

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for release history.

---

## License

Paperdoc Library is proprietary software. See the [LICENSE](LICENSE) file for details.  
© 2024–2026 Paperdoc — [paperdoc.dev](https://paperdoc.dev)
