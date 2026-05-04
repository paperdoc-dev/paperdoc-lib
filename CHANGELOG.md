# Changelog

All notable changes to **paperdoc-lib** are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

> Changes merged into `main` but not yet tagged.

---

## [0.7.0] — 2026-05-04

> **Page layout & text zones** — every page can now be configured
> independently (size, padding, full-page background image or color)
> and absolutely-positioned `TextZone` blocks make pixel-perfect
> layouts trivial. Document-wide running headers/footers with
> placeholder substitution (`{page}`, `{pages}`, `{title}`, `{date}`,
> `{datetime}`) are wired into the PDF and HTML renderers. Fully
> **non-breaking** — every existing `Section` continues to render with
> sensible A4 defaults if no `PageSetup` is attached.

### Added — Page layout

- **`Paperdoc\Enum\PageSize`** — typed enum for the standard formats
  (`A3`, `A4`, `A5`, `A6`, `LETTER`, `LEGAL`, `TABLOID`, `EXECUTIVE`).
  `dimensions()`, `width()` and `height()` return the portrait values
  in PDF points.
- **`Paperdoc\Document\Style\PageSetup`** — value object describing
  the physical page : `width`/`height` (or `fromSize()` /
  `custom($w, $h)`), `orientation` (`portrait` / `landscape` with
  `landscape()` and `portrait()` flippers), `padding` (CSS shorthand —
  1, 2, 3 or 4 values), `backgroundImage` and `backgroundColor`.
  `getContentWidth()` / `getContentHeight()` expose the inner usable
  area. Implements `JsonSerializable`.
- **`Section::setPageSetup(?PageSetup)`** plus convenience setters
  delegating to `PageSetup` : `setPageSize()`, `setPageDimensions()`,
  `setPagePadding()`, `setPageBackgroundImage()`,
  `setPageBackgroundColor()`. Any section can therefore declare its
  own page size and background — chain several sections to obtain
  per-page configurations (cover/portrait, body/landscape, tear-out
  square, etc.).

### Added — Absolute text positioning

- **`Paperdoc\Document\TextZone`** — a new `BlockElementInterface`
  block that places text in an absolutely-positioned rectangle
  (`x`, `y`, `width`, `height` in points, top-left origin to match
  CSS and user expectations). Supports per-zone `padding`,
  `backgroundColor`, `border` (`setBorder($color, $width)`) and
  three overflow strategies :
  - `OVERFLOW_CLIP` (default) — silently truncates content that does
    not fit in the box.
  - `OVERFLOW_ELLIPSIS` — truncates and ends the last visible line
    with an ellipsis (`…`).
  - `OVERFLOW_VISIBLE` — no clipping; content may flow outside the
    box (rarely useful, kept for parity with CSS).
- **`Section::addTextZone($x, $y, $w, $h)`** — fluent shortcut that
  appends a `TextZone` to the section and returns it so paragraphs
  can be added immediately.

### Added — Running headers & footers

- **`Paperdoc\Document\Style\RunningElement`** — value object for a
  document-wide running header or footer. `template`, `alignment`
  (`Paperdoc\Enum\Alignment`) and `style` (`TextStyle`) are
  configurable; `resolve($pageNumber, $totalPages, $title)` substitutes
  the supported placeholders : `{page}`, `{pages}`, `{title}`,
  `{date}` (`Y-m-d`) and `{datetime}` (`Y-m-d H:i`).
- **`Document::setHeader(?RunningElement)`** /
  **`Document::setFooter(?RunningElement)`** — register a running
  header or footer applied uniformly to every page (header/footer is
  drawn after the page background but before content, ensuring it is
  always visible).

### Added — PDF engine

- **`PdfEngine::setPageGeometry($width, $height, $marginTop, $marginRight, $marginBottom, $marginLeft)`**
  — adjusts the active page's `MediaBox` and content margins on the
  fly. Each emitted page now records its own geometry, so a single
  PDF can mix portrait, landscape and custom-size pages within a
  document.
- **`PdfEngine::drawPageBackgroundColor($hex)`** /
  **`drawPageBackgroundImage(Image $image)`** — paint a full-bleed
  background as the very first operation of the current content
  stream, behind every subsequent draw call.
- **`PdfEngine::drawRectTopLeft()`** / **`drawImageTopLeft()`** —
  top-left-origin variants that translate into the PDF
  bottom-left-origin coordinate system internally; used by the
  `TextZone` painter.
- **`PdfEngine::writeWrappedTextAt()`** — now accepts an optional
  `maxHeight` and `ellipsis` string and returns
  `['consumed', 'truncated', 'totalLines', 'drawnLines']`, allowing the
  renderer to honour `OVERFLOW_CLIP` and `OVERFLOW_ELLIPSIS`
  precisely.
- **`PdfEngine::getCurrentPageNumber()`** — 1-indexed accessor used
  by the running-element pipeline.

### Added — Renderers

- **`PdfRenderer`** — full pipeline for the new model:
  - per-section `PageSetup` is applied at section start and after
    every page break (so auto-paginated content keeps its background
    when overflowing to additional pages),
  - `TextZone` is drawn with optional fill/border, then text is
    written through `writeWrappedTextAt()` with the user-selected
    overflow strategy,
  - `Document` header/footer is rendered on every page (initial,
    page-break and final pages alike) with placeholder resolution.
- **`HtmlRenderer`**:
  - `<section class="paperdoc-page">` now reflects per-page width,
    height, padding, `background-color` and `background-image`
    (local files are inlined as `data:` URIs so previews work
    without an HTTP host).
  - `TextZone` becomes a `<div class="paperdoc-text-zone">` with
    `position:absolute`, font/color metrics propagated to the wrapper
    and an inner `<div class="paperdoc-clamp">` whose
    `max-height = N × line-height` clips at an exact integer number
    of lines (much more reliable than `-webkit-line-clamp`, which
    occasionally leaks content past the clamp on fixed-height
    parents). `OVERFLOW_ELLIPSIS` adds an ellipsis pseudo-element in
    the bottom-right corner backed by the zone's own background to
    blend seamlessly.
  - Headers and footers are rendered as `.paperdoc-running.header` /
    `.paperdoc-running.footer` bars with a translucent backdrop and a
    blur filter — guaranteed legible on top of any background image.

### Tests

- **`tests/Unit/Enum/PageSizeTest.php`** — dimensions and helpers for
  every standard format.
- **`tests/Unit/Document/Style/PageSetupTest.php`** — factories
  (`make`, `fromSize`, `custom`), orientation flipping, padding
  shorthand, background setters, content width/height,
  JSON serialisation.
- **`tests/Unit/Document/Style/RunningElementTest.php`** — template,
  alignment, style, height accessors and placeholder resolution
  (including `{date}` / `{datetime}` formatting).
- **`tests/Unit/Document/TextZoneTest.php`** — geometry, padding,
  background, border, overflow validation (`InvalidArgumentException`
  on unknown mode), paragraph addition, JSON serialisation.
- **`tests/Unit/Document/SectionTest.php`** /
  **`tests/Unit/Document/DocumentTest.php`** updated for the new
  setters and JSON output.

### Notes / migration

- 100% non-breaking: any `Section` without a `PageSetup` keeps
  rendering on the historical A4-portrait default with 40-pt margins.
- The library does not automatically reserve vertical space for
  headers / footers — placement of `TextZone` blocks remains under
  the user's control. The HTML renderer adds a translucent strip
  behind the running elements as a built-in safety net so the
  text stays legible on busy backgrounds.
- The standalone `examples/` folder has been removed; minimal
  snippets demonstrating every new API now live directly in
  `README.md` and `resources/views/documents/index.blade.php` so
  package consumers can copy/paste without cloning the repo.

---

## [0.6.0] — 2026-04-29

> Bug-fix release on the road to **1.0**: makes tables and images
> robust across the four built-in renderers. **Non-breaking** — the
> document model and public renderer interfaces are unchanged.

### Fixed — Tables & Images

- **DocxRenderer / HtmlRenderer / MarkdownRenderer / PdfRenderer** — table cells used to silently drop every element that was not a `Paragraph`. They now accept the **full block model** : `Image`, `Heading`, `ListBlock`, `Blockquote`, `CodeBlock`, `Bookmark`. Markdown flattens multi-line content (lists, code, quotes) to a single-line representation so the pipe-table contract is preserved ; PDF surfaces image alt text as a graceful fallback (inline image painting inside cells is tracked for a later release).
- **DocxRenderer** — emits the OOXML-required `<w:tblGrid>` element with one `<w:gridCol w:w="…"/>` per column and a per-cell `<w:tcW w:w="…" w:type="dxa"/>`. `Table::getColumnWidths()` is now honoured proportionally on the document content width (Letter − 2×1″ margins = 9360 twips). Without this, Word displayed warnings and LibreOffice sometimes laid the table out incorrectly.
- **DocxRenderer** — image dimensions are now resolved via `getimagesizefromstring()` when the user does not set `width`/`height`, preserving the real aspect ratio. When only one dimension is supplied the other is computed from the source. Oversized images are capped to the content width while keeping the aspect ratio, preventing overflow into the page margins.
- **HtmlRenderer** — `<img>` is no longer wrapped in `<figure>` — the wrapper broke the layout when images were placed inside `<td>` or `<li>` and added no semantic value. Hosts can wrap output in their own `<figure>` when needed.
- **PdfRenderer** — table cells now detect the dominant `TextStyle` across all of their runs and honour bold / italic / colour / font-size when the runs share a single style.

### Tests

- New `tests/Unit/Renderers/TableContentRenderingTest.php` (13 tests / 42 assertions) covering : image / list / code-block inside cells across the four renderers, OOXML compliance (`<w:tblGrid>`, `<w:tcW>`), automatic image dimension detection, oversized-image capping with aspect-ratio preservation, pipe escaping in Markdown cells, and bold-run rendering inside PDF cells.
- Total suite : **599 tests / 1418 assertions**, no regressions.

---

## [0.5.0] — 2026-04-24

> Second milestone on the road to **1.0**: the renderers catch up with the
> v0.4.0 document model. Every block element now has a proper visual
> representation in DOCX, PDF, HTML and Markdown — no element is silently
> dropped anymore. This release is **non-breaking**: the model, fluent API
> and public renderer interfaces are unchanged.

### Added — Rendering core

- **HtmlRenderer**
  - `Heading` → `<h1>…<h6>` with optional `id` anchor.
  - `ListBlock`/`ListItem` → `<ul>` / `<ol start="N">` with fully nested lists (sublists live inside their parent `<li>`).
  - `Blockquote` → `<blockquote>` wrapping any number of nested block elements (paragraphs, lists, nested quotes…).
  - `CodeBlock` → `<pre><code class="language-…">…</code></pre>` with HTML-escaped content and an optional `language-*` CSS class.
  - `Bookmark` → `<a id="…" class="paperdoc-bookmark"></a>` target for internal `TextLink` anchors.
  - Embedded CSS refreshed: `blockquote`, `pre`/`code`, `ul`/`ol`/`li` rules.

- **MarkdownRenderer**
  - `Heading` → `#`…`######`, with `{#id}` anchor syntax when an id is set.
  - `ListBlock` → `-` (bullet) or `1.` (ordered, starting at `start`), nested lists indented with two spaces, ordered counter increments correctly.
  - `Blockquote` → `> `-prefixed block, preserving inner paragraphs / nested lists.
  - `CodeBlock` → fenced triple-backtick block with language hint.
  - `Bookmark` → inline `<a id="…"></a>` anchor compatible with most Markdown flavours.

- **DocxRenderer** (native OOXML, zero dependencies)
  - `Heading` → `<w:pStyle w:val="HeadingN"/>` with full outline levels **1–6** (up from 1–4). Optional heading `id` is wrapped as a `<w:bookmarkStart/>` / `<w:bookmarkEnd/>` pair.
  - `ListBlock` → real numbered/bulleted lists via `<w:numPr>` referencing per-list `<w:num>` overrides in a new `word/numbering.xml` part. Nested lists honour `<w:ilvl>` up to 8 levels. Ordered lists with a custom `start` emit `<w:startOverride/>`. `ListParagraph` style added.
  - `Blockquote` → `<w:pStyle w:val="Quote"/>` with italic run properties and 720-twip left indent; nested blocks (including nested quotes) keep proper indentation.
  - `CodeBlock` → `<w:pStyle w:val="Code"/>` paragraph with Consolas font and preserved newlines emitted as `<w:br/>`.
  - `Bookmark` → `<w:bookmarkStart/>` / `<w:bookmarkEnd/>`. Names are sanitised to the OOXML `[A-Za-z_][A-Za-z0-9_]*` grammar and capped at 40 chars.
  - `Image` → `<w:drawing>` with a DrawingML inline picture, bytes added to `word/media/imageN.ext` and a fresh `word/_rels/document.xml.rels` image relationship. Embedded (`Image::fromData(...)`) and on-disk images are both supported.
  - `TextLink` (hyperlinks) → `<w:hyperlink>` with dynamically-registered `Hyperlink` relationships (external URLs get `TargetMode="External"`), `w:anchor="…"` for internal/hybrid links and `w:tooltip="…"` when a title is set. Run styling inside a link is preserved and the default link look (blue + underline) is applied on top.
  - `Metadata` → `docProps/core.xml` now maps `author`, `subject`, `description`, `keywords`, `language`, `createdAt`, `modifiedAt` from `Document::getProperties()`.
  - `Content_Types.xml`, relationships and the new `numbering.xml` part are wired correctly so the produced DOCX opens cleanly in Word and LibreOffice.

- **PdfRenderer** (native, zero dependencies)
  - `Heading` → typed headings with level-based font sizes (24/20/16/14/13/12 pt) and a default navy accent.
  - `ListBlock`/`ListItem` → bullet (`•`) and decimal markers with nested-depth indentation; ordered counter correctly continues from `ListBlock::getStart()`.
  - `Blockquote` → indented, italic, muted-grey paragraphs. Nested block elements are indented recursively.
  - `CodeBlock` → Courier-rendered lines with dedicated spacing.
  - `Image` → both on-disk (`Image::make($path)`) and embedded (`Image::fromData($bytes, $mime)`) images are drawn via the existing `PdfEngine::drawImage` path; embedded data is materialised to a temp file under the hood.
  - `TextLink` → visible styling (blue + underline) for link runs (actual clickable annotations come later).
  - `Metadata` → `Document::getProperties()->getAuthor()` becomes the PDF `/Creator` when set.

### Fixed

- **Images silently dropped in PDF (GIF, WebP, broken PNG)** — `Paperdoc\Support\Pdf\PdfEngine::registerImage` used to register `images` / `imageRefs` *before* knowing whether the XObject body could actually be built (PNG → GD pipeline could fail, GIF / WebP / etc. were never written at all). The PDF then referenced an empty object and showed nothing. Now the engine routes everything through `imagecreatefromstring` + `imagejpeg` first (covers GIF, WebP, PNG, BMP, …), falls back to direct DCT for JPEG, then to `imagecreatefrompng` on the file path, and only registers the reference once a valid JPEG payload is available.
- **Embedded images broken in HTML & Markdown** — `HtmlRenderer::renderImage` and `MarkdownRenderer::renderImage` only switched to a data URI when `Image::getSrc() === ''`, but `Image::fromData()` initialises `src` to a placeholder like `embedded.png`. Result: `<img src="embedded.png">` (a dead link) or `![alt](embedded.png)` instead of an inlined data URI. Both renderers now prefer the data URI whenever `Image::hasData()` is true.
- **DOCX `<a:blip>` missing namespace** — added an explicit `xmlns:r` on `pic:blipFill > a:blip` so Word and LibreOffice resolve the image relationship even when the package is opened by stricter readers.
- **DOCX `[Content_Types].xml` missed `webp`** — added `webp` to the default extension map.
- **Spacing between block elements in PDF** — the native PDF engine draws text at the *baseline* of the cursor, so a 24 pt heading visually rose ~19 pt above the cursor, causing headings to overlap the previous block (visible between a table and the following heading). `PdfRenderer::writeHeading` (and the legacy `Paragraph + ParagraphStyle::headingLevel` path) now compensates for the font ascent before drawing. Tables (+12 pt after), images (+10 pt after), root-level lists (+10 pt after) and blockquotes (+6/+10 pt) also get tighter, more consistent surrounding gaps.
- **DOCX heading spacing** — `Heading1`…`Heading6` now use `w:after="220"` (was `80`) for clearer separation in Word/LibreOffice.

### Added — Tests

- **`tests/Unit/Renderers/HtmlRendererModelCoreTest`** (11 tests) — headings, bullet/ordered/nested lists, blockquote, code block (with / without language), bookmarks, internal + external links.
- **`tests/Unit/Renderers/MarkdownRendererModelCoreTest`** (11 tests) — heading + `{#id}` syntax, nested-list indentation, blockquote formatting, fenced code blocks, anchor emission, link + emphasis preservation.
- **`tests/Unit/Renderers/DocxRendererModelCoreTest`** (13 tests) — unzips the produced DOCX and asserts on the generated `word/document.xml`, `word/numbering.xml`, `word/_rels/document.xml.rels` and `docProps/core.xml` (heading `pStyle`, bookmarks, `numPr`/`ilvl`, ordered `startOverride`, `Quote`/`Code` styles, external + anchor hyperlinks with tooltip, embedded image media + rels, typed metadata).
- **`tests/Unit/Renderers/PdfRendererModelCoreTest`** (8 tests) — decompresses PDF content streams and asserts on rendered text / markers / colors for headings, lists, code blocks, blockquotes and external links.
- **`tests/Unit/Renderers/ImageRenderingTest`** (11 tests) — cross-renderer image coverage with two real fixtures (`tests/Fixtures/Images/paperdoc-logo.png`, `tests/Fixtures/Images/dot.gif`) exercising both `Image::make($path)` and `Image::fromData()` in DOCX, PDF, HTML and Markdown — including PDF GIF re-encoding, two-images-distinct-relationships in DOCX, missing-file safety in PDF, and data-URI embedding in HTML/Markdown.
- **`tests/Unit/Support/Pdf/PdfEngineTest`** — added `test_draw_image_gif_is_embedded_as_dct` covering the new GIF→JPEG (DCT) re-encoding path.
- **+54 new tests / +149 new assertions** in total, all green alongside the existing suite (now 586 tests / 1 375 assertions).

---

## [0.4.0] — 2026-04-24

> First milestone on the road to **1.0**: a richer, strongly-typed document
> model. This release is **non-breaking** — existing parsers/renderers keep
> working; the new elements become natively rendered in later releases.

### Added — Document model (road to 1.0)
- **`Paperdoc\Document\ListBlock`** + **`ListItem`** — first-class ordered/unordered lists with nested lists, custom starting number, styled item labels (runs + link support). Wraps `DocumentElementInterface` so it can be added anywhere a block element is expected.
- **`Paperdoc\Document\Heading`** — typed heading element (levels 1–6, optional `id` anchor, styled runs). Replaces the bancal "Paragraph + ParagraphStyle::headingLevel" pattern while remaining backward-compatible: the legacy pattern still works.
- **`Paperdoc\Document\Bookmark`** — named landmark element completing the v0.3.8 hyperlink feature: `TextLink::anchor` can now point to a declared `Bookmark::id`.
- **`Paperdoc\Document\CodeBlock`** — verbatim source block with optional language hint (maps cleanly to Markdown fences and `<pre><code class="language-…">`).
- **`Paperdoc\Document\Blockquote`** — nested-aware quoted block.
- **`Paperdoc\Document\Metadata`** — typed document properties (author / subject / description / keywords / created-at / modified-at / language), exposed on `Document::getProperties()` / `setProperties()`. Separate from the existing loose `metadata` key/value bag.
- **Marker interfaces** — `Paperdoc\Contracts\BlockElementInterface` and `InlineElementInterface`, both extending `DocumentElementInterface`. Existing `Paragraph`, `Image`, `PageBreak`, `Table` are retrofitted to `BlockElementInterface` (non-breaking).
- **Typed exceptions** — new `Paperdoc\Exceptions\` namespace: `PaperdocException` (base), `ParserException`, `RendererException`, `UnsupportedFormatException`, `InvalidDocumentException`. Factory helpers (`ParserException::forFile()`, `UnsupportedFormatException::forFormat()`, …) for readable error messages.

### Added — Section fluent API
- `Section::addList($style, $start)`, `addBulletList()`, `addOrderedList($start)` — return the created `ListBlock` so items can be added fluently.
- `Section::addBookmark($id)` — returns the created `Bookmark`.
- `Section::addCodeBlock($code, $language)` — returns the created `CodeBlock`.
- `Section::addBlockquote()` — returns the created `Blockquote`.

### Tests
- 80+ new unit tests across `tests/Unit/Document/{ListBlock,ListItem,Heading,Bookmark,CodeBlock,Blockquote,Metadata}Test.php` and `tests/Unit/Exceptions/ExceptionsTest.php`, plus additional coverage for the new `Section`/`Document` shortcuts.
- Fixed a previously risky test (`DocxParserTest::test_parse_real_docx_with_hyperlink`) so it now explicitly skips when the optional fixture is missing.

---

## [0.3.8] — 2026-04-24

### Added
- **DOCX hyperlink parsing** — `DocxParser` now recognises `<w:hyperlink>` elements and attaches a `TextLink` to the produced `TextRun`s. Supports external URLs (via `r:id` relationship), internal bookmark anchors (`w:anchor`) and tooltips (`w:tooltip`).
- **`Paperdoc\Document\Link\TextLink`** — new link value object with `url`, `anchor`, `title`, a combined `getHref()` helper (`url#anchor`), and an `isExternal()` predicate for http/https/mailto/tel/ftp schemes.
- **`Paperdoc\Contracts\LinkInterface`** — minimal serialisation contract (`toArray()`).
- **`TextRun` / `Section::addText()`** — accept an optional `TextLink` so links can be expressed programmatically.
- **HTML hyperlink rendering** — `HtmlRenderer` renders `<a>` elements with `href`, preserved run styling (`style` attribute for bold/italic/color/font), optional `title`, and `target="_blank" rel="noopener noreferrer"` for external links (tabnabbing safety).
- **Markdown hyperlink rendering** — `MarkdownRenderer` produces safe `[label](url "title")` syntax: brackets inside the label are escaped, URLs containing spaces or parentheses are wrapped in `<…>`, and titles are quoted.
- **Tests** — new test coverage for hyperlink parsing (`DocxParserTest::test_parse_docx_with_hyperlink`) and rendering (`HtmlRendererTest::test_renders_hyperlink`, `MarkdownRendererTest::test_renders_hyperlink`).

### Fixed
- Hyperlinks without a resolvable `r:id` (internal bookmarks, broken relationships) no longer silently drop their inner text. The runs are still emitted, with an anchor-only link when available.
- HTML renderer no longer discards run styling when a link is present.
- Markdown renderer no longer emits malformed `[text](url)` when the label or URL contains markdown-sensitive characters.

### Credits
- Hyperlink feature implemented by **Olivier Mourlevat** ([@olivM](https://github.com/olivM)) in PR [#4](https://github.com/paperdoc-dev/paperdoc-lib/pull/4) — first external contribution. Thank you!

---

## [0.3.7] — 2026-04-17

### Fixed
- **DOCX generation — "Word experienced an error trying to open the file"**: `DocumentFactory` mapped the `docx` format to `HtmlRenderer`, so `DocumentManager::save($doc, 'file.docx')` wrote HTML bytes into a file with a `.docx` extension. Word, LibreOffice and any OOXML reader rejected the result because it was not a valid ZIP/OOXML package.

### Added
- **`Paperdoc\Renderers\DocxRenderer`** — native Office Open XML (WordprocessingML) renderer built on `ZipArchive`, with **zero third-party dependencies**. Produces a valid `.docx` package (`[Content_Types].xml`, `_rels/.rels`, `word/document.xml`, `word/_rels/document.xml.rels`, `word/styles.xml`, `docProps/core.xml`, `docProps/app.xml`).
- Run styling: bold, italic, underline, color, font family, font size (half-points).
- Paragraph styling: alignment (`left`/`center`/`right`/`both`), spacing before/after, line spacing, heading levels (`Heading1`..`Heading4` via `pStyle` + `outlineLvl`).
- Tables: rows, cells, header rows (`tblHeader`), `gridSpan` (colspan), single-line borders.
- Page break support via the `PageBreak` element.
- Proper XML escaping and `xml:space="preserve"` for whitespace-sensitive runs; line breaks inside a run become `<w:br/>`.
- Round-trip safe with `DocxParser` (text content is preserved).

### Changed
- **`DocumentFactory`**: `'docx' => DocxRenderer::class` (was `HtmlRenderer`).

---

## [0.3.6] — 2026-04-16

### Added
- **`Document::openSection(?Section $section = null): Section`** — appends a section and returns it so callers can chain `addParagraph()`, `addHeading()`, `addElement()`, etc., without manually wiring `Section::make()` then `addSection()`.
- **`Section::addParagraph(string $text, ?TextStyle $style = null): Paragraph`** — alias of `addText()` for clearer quick-start style code.

### Changed
- **`Document::addSection()`** — the `Section` argument is now optional (`null` appends an unnamed empty section via `Section::make()`). Existing calls that pass a `Section` instance are unchanged.
- **`DocumentInterface`** — signatures updated for optional `addSection` and new `openSection`.

### Documentation
- **README** — Quick Start aligned with the real API: static `DocumentManager::create` / `save`, `openSection()` + `addParagraph()` with `TextStyle` for bold, Laravel facade example updated; short note on the document vs section model and link to `examples/usage.php`.

---

## [0.3.5] — 2026-03-04

### Changed
- **Document::getThumbnail()** — thumbnail from source file (Imagick/Ghostscript/LibreOffice) is now tried **first**; embedded document images (e.g. signature/cachet) are used only as fallback when the source file is unavailable. Fixes thumbnails showing only the signature instead of the full page for PDFs opened via `DocumentManager::open()` when using `getThumbnailDataUri()` or `documentThumbnailDataUri()`.

---

## [0.3.4] — 2026-03-04

### Added
- **PdfParser — Object Streams (ObjStm)**: PDFs using compressed object streams (PDF 1.5+, `/Type /ObjStm`) are now parsed correctly. Pages and other objects stored inside ObjStm were previously invisible to the parser (0 sections). New `unpackObjectStreams()` runs after `parseObjects()`, decompresses each ObjStm, parses the header (objNum/offset pairs), and injects embedded objects into the object table so page discovery and content extraction work as usual.

---

## [0.3.3] — 2026-03-03

### Added
- **PdfParser — ToUnicode CMap + hex text operators**: PDFs using hex string text (`<XXXX> Tj` / `<XXXX> TJ`) with ToUnicode CMaps (e.g. Chrome/Electron-generated PDFs) are now decoded correctly. New helpers: `buildFontCMaps()`, `parseCMapStream()`, `resolvePageFontMap()`, `decodeHexViaCMap()`; `parseTextBlockWithCtm()` tracks current font and decodes hex via CMap.
- **PdfParser — recursive image extraction**: `findImageXObjectRefs()` now recurses into Form XObjects (depth limit 5) so images inside nested Form XObjects (e.g. scanned ID cards, diplomas) are extracted.
- **PdfParser — garbage text filter**: `isGarbageText()` rejects text that is mostly binary/noise (misidentified image streams or garbled OCR). Uses ratio of ASCII letter sequences (2+ chars) and digit sequences (2+ chars) vs total length; threshold 0.15. Applied in `sortAndBuildElements()` after CID spacing collapse.
- **CsvParser — row limit**: `MAX_ROWS = 10_000` to avoid OOM on very large CSV files (e.g. hundreds of MB).
- **XlsxParser — sheet size limit**: `MAX_SHEET_SIZE = 50 * 1024 * 1024` bytes per sheet (compressed) to avoid OOM when decompressing huge worksheets.

### Changed
- **PdfParser**: `extractTextLines()` and `parseTextBlockWithCtm()` accept optional `$fontMap`; `extractTJText()` accepts optional `$cmap` for hex strings in TJ arrays. Combined Y-group text is passed through `collapseCidSpacing()` before adding to the section; garbage text is skipped.

---

## [0.3.2] — 2026-03-02

### Changed
- **Thumbnail pipeline** — real rendering is now preferred for correct fonts and layout:
  - **PDF**: Imagick → Ghostscript → native (text/image) fallback (was: native first). Enables proper page rendering when Imagick or Ghostscript is installed.
  - **OOXML (DOCX, XLSX, PPTX)**: LibreOffice first (convert to PDF, then thumbnail), then embedded thumbnail or GD preview. **LibreOffice is required** for high-quality Office thumbnails.
  - **CSV/TSV**: LibreOffice first; when unavailable, new native `renderCsvPreview()` shows first rows as a grid (badge "CSV").
- **ThumbnailGenerator** class docblock updated to describe the new pipeline order.
- **renderGridPreview()** — added optional `$badge` parameter (default `'XLSX'`) for CSV grid preview.

### Added
- **renderCsvPreview()** — native CSV/TSV thumbnail when LibreOffice is not available: reads first lines, parses delimiter (`,` or tab), renders via existing grid preview.

### Documentation
- Thumbnail requirements clarified: **LibreOffice** required for DOCX, XLSX, PPTX, CSV; **Imagick** or **Ghostscript** required for PDF thumbnails with correct rendering.

---

## [0.3.1] — 2026-03-02

### Changed
- **PDF thumbnails** — text content is now preferred over embedded images:
  - `renderPdfNative()` extracts and renders text first; embedded images are used only when no text is found
  - `extractPdfEmbeddedImage()` scans all embedded JPEG/PNG, skips small images (e.g. header logos) via minimum area (`PDF_MIN_IMAGE_AREA`), and selects the largest qualifying image
- **DOCX thumbnails** — styled preview instead of plain text:
  - New `parseDocxStyledParagraphs()` parses `w:pPr` / `w:rPr` for Heading1–3, Title, Subtitle, bold, italic
  - New `renderStyledPreview()` with style-aware rendering: font size, line height, underline for titles, spacing
- **PPTX thumbnails** — use `renderStyledPreview()` with first paragraph as title (h1), rest as body

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

[Unreleased]: https://github.com/paperdoc-dev/paperdoc-lib/compare/v0.4.0...HEAD
[0.4.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.4.0
[0.3.8]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.3.8
[0.3.7]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.3.7
[0.3.6]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.3.6
[0.3.5]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.3.5
[0.3.4]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.3.4
[0.3.3]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.3.3
[0.3.2]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.3.2
[0.3.1]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.3.1
[0.3.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.3.0
[0.2.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.2.0
[0.1.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.1.0
