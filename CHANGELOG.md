# Changelog

All notable changes to **paperdoc-lib** are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

> Changes merged into `develop` but not yet released.

---

## [0.3.0] — 2026-03-02

### Added
- **Thumbnail base64 API** — generate thumbnails without saving to disk, as raw base64 strings:
  - `ThumbnailGenerator::fromFileBase64()` — from any supported file path
  - `ThumbnailGenerator::generateBase64()` — from an `Image` element
  - `DocumentInterface::getThumbnailBase64()` — on documents and images
  - `DocumentManager::thumbnailBase64()` — static helper
  - `InteractsWithPaperdoc::documentThumbnailBase64()` — trait helper
  - `Paperdoc` Facade PHPDoc updated with `thumbnail`, `thumbnailDataUri`, `thumbnailBase64`
- **Native thumbnail generation (no third-party binaries)** for PDF, DOCX, XLSX, PPTX:
  - PDF: extract embedded JPEG/PNG or parse text operators (Tj, TJ), render preview via GD
  - OOXML: extract embedded `docProps/thumbnail.*` from ZIP, or render text/grid preview via GD (DOCX paragraphs, XLSX sheet grid, PPTX slide text)
  - LibreOffice, Imagick, Ghostscript remain optional fallbacks when native path fails
- **ThumbnailGeneratorTest** — 28 unit tests for resize, fromFile (image/PDF/DOCX/XLSX), fromFileDataUri, fromFileBase64, generate/generateBase64, capabilities, edge cases

### Changed
- **ThumbnailGenerator** — code quality improvements:
  - Class marked `final`
  - Magic numbers replaced by named constants (preview/grid dimensions, PDF limits, OOXML namespaces)
  - DRY helpers: `openZip()`, `readFromZip()`, `createPreviewCanvas()`, `previewColors()`, `gdToPng()`, `gdToPngAndResize()`, `extractXmlTextNodes()`, `decompressStream()`, `extractPdfParagraphs()`
  - `extractPdfEmbeddedImage()` — unified JPEG/PNG detection via signature list
  - `findBinary()` — use `escapeshellarg($name)` for safe shell invocation
  - `tempPath()` — remove orphan temp file created by `tempnam()` before adding extension
  - Removed all `imagedestroy()` calls (deprecated in PHP 8.5; GD resources are now `GdImage` objects)
- **PDF test fixtures** — valid xref/startxref so Ghostscript does not emit errors when used as fallback

---

## [0.2.0] — 2026-03-01

### Added
- **DocRenderer** — native Word 97-2003 (.doc) generation via OLE2 + FIB + piece table
- **XlsRenderer** — native Excel 97-2003 (.xls) BIFF8 generation with SST, multi-sheets
- **PptRenderer** — native PowerPoint 97-2003 (.ppt) generation with SlideListWithText
- **Ole2Writer** — OLE2 Compound Binary File writer for legacy Office formats
- DOC, XLS, PPT now support both parse and render (see README Supported Formats table)
- DOCX fallback renderer (HTML) in DocumentFactory

### Changed
- `DocumentFactory` — register dedicated renderers for doc, xls, ppt (no longer fallback to docx/xlsx/pptx)
- `DocumentFactoryTest` — unsupported format test uses `bmp` instead of `docx`

---

## [0.1.0] — 2026-03-01

> ⚠️ Early-stage release — API is not yet stable and may change between minor versions.

### Added
- `DocumentManager` — central entry point for create / open / save / convert / renderAs / openBatch
- **Parsers**: PDF, HTML, DOCX, DOC, XLSX, XLS, PPTX, PPT, CSV, Markdown
- **Renderers**: PDF, HTML, XLSX, PPTX, CSV, Markdown
- Unified in-memory document model: `Document`, `Section`, `Paragraph`, `TextRun`, `Table`, `TableRow`, `TableCell`, `Image`, `PageBreak`
- `Style/` sub-system for paragraph and run-level formatting
- Laravel integration — `PaperdocServiceProvider` and `Paperdoc` Facade (auto-discovery)
- Artisan console commands via `Console/`
- AI/LLM extraction layer via `Llm/` (powered by Neuron AI `^3.0`)
- OCR integration via `Ocr/` with post-processing pipeline
- Contract interfaces: `DocumentInterface`, `ParserInterface`, `RendererInterface`
- Enum-based format registry (`Enum/`)
- Factory classes for documents and parsers (`Factory/`)
- Full PHPUnit 11 test suite (Unit + Integration)
- `phpunit.xml` configuration
- `config/paperdoc.php` with default format, text styles, storage, and AI settings
- Repo hygiene: `README.md`, `CONTRIBUTING.md`, `CHANGELOG.md`, `CODEOWNERS`
- `.github/release.yml` and `.github/FUNDING.yml`

### Requirements
- PHP ^8.2
- ext-dom, ext-mbstring, ext-zip, ext-zlib
- neuron-core/neuron-ai ^3.0

---

[Unreleased]: https://github.com/paperdoc-dev/paperdoc-lib/compare/v0.3.0...HEAD
[0.3.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.3.0
[0.2.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.2.0
[0.1.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.1.0
