# Changelog

All notable changes to **paperdoc-lib** are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

> Changes merged into `develop` but not yet released.

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

[Unreleased]: https://github.com/paperdoc-dev/paperdoc-lib/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.1.0
