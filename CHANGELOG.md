# Changelog

All notable changes to **paperdoc-lib** are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

> Changes merged into `main` but not yet tagged.

## [1.1.5] — 2026-08-29

### Fixed

- **PdfParser** — map font size, bold, italic, and font family from PDF `/Tf` operators and font descriptors into `TextStyle` runs.
- **PdfParser** — carry the active font across separate `BT`/`ET` blocks and multiple page content streams so Identity-H hex `TJ` strings decode with the correct ToUnicode CMap.
- **PdfParser** — parse PDF literal strings with escaped parentheses (`\(` / `\)`) in `Tj`/`TJ` operators.

## [1.1.4] — 2026-08-29

### Fixed

- **PdfParser** — fix FlateDecode RGB image PNG export: IHDR width/height now use 32-bit fields (was 16-bit), which broke Word-exported images into unreadable empty boxes.

## [1.1.3] — 2026-08-29

### Fixed

- **PdfParser** — preserve literal spaces inside `Tj`/`TJ` strings (e.g. Word `(s )`, `(et )`) instead of trimming them away per segment.
- **PdfParser** — stop treating negative TJ kerning values as word breaks; numeric entries only adjust glyph positioning per the PDF spec.
- **PdfParser** — join same-line text segments by concatenation in X order without injecting extra spaces, fixing split-word artefacts such as `objecti f`, `M inds` across fonts and languages.

## [1.1.2] — 2026-08-29

### Fixed
- **PdfParser** — stricter table detection to avoid false tables from multi-column prose and TOC layouts.
- **PdfParser** — extract text highlights from background fill rectangles and `/Highlight` annotations into `TextStyle`.


## [1.1.0] — 2026-08-17

> **Internationalisation release.** The library rendered and re-read
> non-Latin text correctly in every format except two — PDF and DOC,
> both of which encoded text as Windows-1252 and turned Cyrillic,
> Greek, Arabic, Hebrew, CJK and Thai into `?`. That gap is closed,
> along with the OLE2 container defects that made `.doc`/`.xls`/`.ppt`
> unreadable, and a set of parser bugs that silently dropped or
> corrupted content.

### Added

- **TrueType/OpenType font embedding for PDF** —
  `PdfRenderer::registerTrueTypeFont($alias, $path, $fontIndex = 0)`
  makes a font available as a `TextStyle` font family. Text set in it
  is written as glyph ids through a `Type0` / `Identity-H` font, which
  lifts the Latin-1 ceiling of the 14 standard fonts. Accepts `.ttf`,
  `.otf` (CFF outlines, embedded as `CIDFontType0` / `FontFile3`) and
  `.ttc` collections (`$fontIndex` picks the face). A `ToUnicode` CMap
  is emitted so the text stays selectable and extractable.
  **The library ships no font data** — the caller supplies the file and
  is responsible for its embedding licence.
- **Glyph subsetting.** Only the glyphs actually used are embedded,
  with composite components pulled in. A Latin page drops from 423 KB
  to ~9 KB, a CJK page from 4 MB to ~4 KB. CFF outlines are embedded
  whole (subsetting them would mean rewriting the CFF charstrings).
- **Unicode bidirectional algorithm** (`Paperdoc\Support\Text\Bidi`) —
  rules P2–P3, W1–W7, N0–N2, I1–I2 and L1–L4. PDF places glyphs where
  it is told, so without reordering an Arabic or Hebrew line came out
  backwards. Verified against `fribidi`: 21 of 21 mixed-script cases
  byte-identical.
- **Arabic contextual shaping**
  (`Paperdoc\Support\Text\ArabicShaper`) — isolated/initial/medial/final
  presentation forms and lam-alef ligatures, applied in logical order
  before reordering. Without it the letters render disconnected.
- **`Paperdoc\Support\TextDirection`** — detects whether text is
  predominantly right-to-left (8 RTL scripts).
- **Line breaking for scripts without word spaces.** `wrapText()` now
  breaks inside an over-long run on grapheme-cluster boundaries, so
  Japanese, Chinese and Thai paragraphs — and very long URLs — no
  longer overflow the page on a single line.

### Changed

- **`.doc` text is written as uncompressed UTF-16LE** instead of
  Windows-1252, matching what `PptRenderer` and `XlsRenderer` already
  did. Every script now survives a `.doc` round-trip. *The bytes in the
  file change: code reading the raw stream for ASCII will need
  updating.*
- **HTML output declares its direction** — `<html lang="…" dir="ltr">`
  or `dir="rtl"`, letting the browser run the bidi algorithm.
- **DOCX marks right-to-left content** with `<w:bidi/>` on the
  paragraph and `<w:rtl/>` on the runs.
- PDF text is shaped and reordered on the way out. Text with no
  right-to-left character is passed through untouched.

### Fixed

- **`Ole2Writer` produced malformed containers.** The header omitted
  the 16-byte CLSID at offset 8, making it 496 bytes instead of 512 and
  shifting every field; and streams under 4096 bytes were stored in
  normal sectors instead of the mini-stream that [MS-CFB] requires.
  Consequence: a `.doc` written by the library exhausted memory when
  read back, and `.xls`/`.ppt` came back **empty with no error**.
- **`Ole2Reader` could be made to exhaust memory.** `readStream()`
  followed the FAT chain with no cycle guard and an unbounded size for
  the directory and mini-FAT, so a 1.5 KB file with a self-referencing
  FAT entry killed the process with an unrecoverable fatal error.
- **`DocParser` read a `FibRgCswNew` that Word 97 files do not have**
  (it exists only from `nFib` 0x0101), consuming the start of the text
  and pushing the text offset outside the stream. It also rejected
  `fcClx = 0`, a perfectly valid offset into the table stream.
- **`PdfParser` decoded WinAnsi text as ISO-8859-1**, destroying the 27
  printable characters in the 0x80–0x9F range — `€ • – — " " … œ ™` and
  the rest became invisible C1 controls.
- **`PdfParser` discarded every non-Latin page.** `isGarbageText()`
  counted only `[a-zA-Z]` as readable, so a document entirely in
  Russian, Japanese, Greek or Arabic came back empty.
- **`PdfParser` only detected UTF-16BE for ASCII text**, since it
  counted null high bytes. Now keyed on the dominant high byte, which
  covers Cyrillic, Greek, Hebrew, Arabic and Thai without any false
  positive on single-byte text.
- **`MarkdownParser` corrupted tables containing an escaped pipe.**
  `MarkdownRenderer` correctly writes `\|`, but the parser split on
  every `|`, splitting the cell in two and shifting the whole row.
- **`HtmlParser` dropped content outside `<section>`.** As soon as one
  `<section>` existed anywhere, only sections were parsed — a table
  before the first one vanished entirely.
- **`HtmlParser` flattened unhandled wrappers.** `<aside>`,
  `<blockquote>`, `<form>`, `<details>`… collapsed their whole subtree
  to text, turning a nested table into a single run.
- **`HtmlParser` fell back to ISO-8859-1 when the body used the word
  "encoding".** The charset check scanned the whole file; it now looks
  only at the prologue, and understands `<meta charset>`,
  `<meta http-equiv>` and the XML declaration.
- **`decodePdfString()` trimmed each `TJ` literal**, deleting the
  word spaces carried by `( )` fragments and running whole sentences
  together.
- The PDF `/W` width array assumed consecutive CIDs, which breaks for a
  font embedded with its original glyph ids.

### Tests

- `InternationalisationTest` — 6 formats × 8 scripts round-trip, charset
  detection, garbage filter, UTF-16BE detection, direction, DOCX bidi
  markers.
- `BidiTest` — reordering and shaping, expectations verified against
  `fribidi`.
- `EmbeddedFontTest` — metrics, cmap subtable merging, subset size,
  composite glyphs, `.ttc` collections, the public renderer path.
- `Ole2WriterTest` / `Ole2ReaderTest` — 512-byte header, mini-stream
  round-trip, mixed stream sizes, cyclic FAT with a memory ceiling.

### Known limitations

- CFF (`.otf`) fonts are embedded whole; only TrueType outlines are
  subsetted.
- Variable fonts (`CFF2`) are rejected with an explicit message — a
  static instance is required.
- Explicit bidi embedding controls (LRE, RLE, LRO, RLO, PDF, LRI, RLI,
  FSI, PDI) are treated as boundary-neutral rather than pushed on a
  stack.
- `BidiClass` ranges are derived rather than exhaustive; unlisted
  characters fall back to `L`/`ON` as the standard prescribes.

---

## [1.0.0] — 2026-07-15

> **First stable release.** 🎉 The public API is now covered by the
> semantic-versioning contract: no breaking change will ship without a
> major version bump. This release closes the long-standing "roadmap"
> items of the rendering matrix (clickable PDF links, PDF outline,
> HTML head metadata, Markdown frontmatter) and makes the library
> truly **zero-dependency**: the AI layer's `neuron-ai` requirement is
> replaced by built-in, SDK-free HTTP providers.

### Added

- **Clickable PDF hyperlinks (`/Annots`).** `TextLink` runs are now
  real PDF Link annotations, not just blue underlined text:
  - External URLs (`http`, `https`, `mailto`, …) become `/A << /S /URI >>`
    actions — the full `getHref()` (URL + optional fragment) is used.
  - Anchor links (`TextLink::make('', 'my-anchor')`) become internal
    GoTo destinations targeting the page and vertical position of the
    matching `Bookmark` or `Heading` id — **forward references work**
    (a table of contents on page 1 can point at a heading on page 12).
  - Multi-line (wrapped) links produce one clickable rectangle per
    rendered line, and links crossing an automatic page break attach
    each rectangle to its own page.
  - Links inside `TextZone` blocks are clickable too.
  - A link pointing at an anchor that is never declared is silently
    dropped (the text still renders, it just isn't clickable).
- **PDF document outline (bookmarks panel, `/Outlines`).** Every
  `Heading` (levels 1–6) becomes an outline entry; the tree hierarchy
  is rebuilt from heading levels (skipped levels are clamped so the
  tree stays well-formed), and the catalog opts into
  `/PageMode /UseOutlines` so readers show the panel by default.
- **Full PDF `/Info` dictionary.** `Metadata` now maps to `/Title`,
  `/Author`, `/Subject`, `/Keywords`, `/CreationDate` and `/ModDate`
  (previously only `/Creator` was written). Empty fields are omitted.
- **HTML head metadata.** `Metadata` emits `<meta name="author">`,
  `description` (falls back to `subject`), `keywords`,
  `dcterms.created` / `dcterms.modified`, and the `<html lang>`
  attribute now uses `Metadata::getLanguage()` (default `en` — it was
  previously hardcoded to `fr`).
- **Markdown YAML frontmatter for typed properties.** `title`,
  `author`, `subject`, `description`, `keywords`, `language`,
  `created`, `modified` are merged with the existing loose metadata
  bag (typed keys win). YAML-sensitive values (colons, quotes, …) are
  emitted as JSON-quoted scalars, which are valid YAML.
- **Native LLM provider layer** — the AI features (OCR post-correction
  via `enhance()`, structured extraction via `structureDocument()`) now
  ship with **built-in HTTP clients** for OpenAI (and any
  OpenAI-compatible endpoint via `base_url`), Anthropic, Gemini and
  Ollama: `Paperdoc\Llm\Providers\*` implementing the new
  `Paperdoc\Contracts\LlmProviderInterface`. Vision (page image) input
  is supported by all four. Transport prefers ext-curl and falls back
  to PHP streams. Structured extraction requests strict JSON and
  retries up to 3 times before throwing.
- **`Paperdoc\Exceptions\LlmException`** — typed error for LLM
  transport/HTTP/response failures (`forHttp`, `forTransport`,
  `forResponse`).
- **`TableOfContents` block element** (`Section::addTableOfContents($maxLevel, $title)`).
  Auto-generated from the document's headings, once per renderer:
  - **PDF**: indented, clickable entries (internal GoTo links to each
    heading — anchors are generated automatically for headings without
    an explicit id).
  - **HTML**: `<nav class="paperdoc-toc">` with anchor links; headings
    get auto-generated ids only when a TOC is present.
  - **Markdown**: nested link list using GitHub-style slugs.
  - **DOCX**: native Word `TOC` field (readers offer "Update Field"
    to fill in page numbers).
- **`Watermark`** (`Document::setWatermark(Watermark::make('DRAFT'))`).
  Rotated, semi-transparent text stamped on every page — including
  auto-created continuation pages. Configurable text, font, size,
  color, opacity and angle. PDF uses a native `ExtGState` alpha +
  rotation matrix; HTML uses a centred, rotated overlay `<div>`.
- **Rich text styles** — `TextStyle::setStrikethrough()` and
  `TextStyle::setHighlight(?string $color)` across all four renderers
  (`<w:strike/>` / `<w:shd>` in DOCX, `text-decoration` /
  `background-color` in HTML, `~~text~~` in Markdown). The PDF engine
  now **actually draws** underline, strikethrough and highlight
  rectangles (underline was previously style-only and silently
  ignored in PDF output).
- **String I/O** — `DocumentManager::openString($content, $format)`
  parses a document from raw content without a source file, and
  `DocumentManager::convertString($content, $from, $to)` converts
  between formats fully in memory.
- **`Paperdoc\Enum\Format` accepted everywhere** — `create()`,
  `save()`, `renderAs()`, `convert()`, `openString()`,
  `convertString()`, `Document::make()` and the factories accept
  `Format|string`, e.g. `DocumentManager::create(Format::PDF)`.
- **DOCX running headers/footers** — `Document::setHeader()` /
  `setFooter()` are now honoured by the DOCX renderer (previously PDF
  and HTML only): native `header1.xml` / `footer1.xml` parts wired
  through `headerReference`/`footerReference`, with `{page}` /
  `{pages}` emitted as live Word `PAGE` / `NUMPAGES` fields and
  `{title}` / `{date}` / `{datetime}` resolved at generation time.
  Validated by LibreOffice round-trip (footer present on every page).
- **PDF content streams are Flate-compressed** by default — typical
  documents shrink 3-5×. `PdfEngine::setCompression(false)` restores
  human-readable streams for debugging.
- **CI workflow** (`.github/workflows/tests.yml`) — the README badge
  now points at a real matrix build (PHP 8.2 / 8.3 / 8.4 / 8.5).
- **PdfEngine public API**: `beginLink()` / `endLink()`,
  `registerAnchor()`, `addOutlineEntry()`, `setAuthor()`,
  `setSubject()`, `setKeywords()`, `setCreationDate()`,
  `setModificationDate()`, `setTextDecorations()` /
  `clearTextDecorations()`, `drawWatermarkText()`,
  `setCompression()`.

### Changed

- **`neuron-core/neuron-ai` is removed entirely.** The library now has
  **zero Composer dependencies** — including the AI layer, which is
  implemented natively (see above). `LlmAugmenter`'s public API and the
  `config/paperdoc.php` shape (`provider`, `model`, `api_key`,
  `base_url`, `options`) are unchanged;
  `DocumentManager::open(['llm' => true])` behaves as before.
  `LlmAugmenter` also accepts an optional `LlmProviderInterface` as a
  second constructor argument for custom providers or testing.
- `Heading` runs carrying a `TextLink` keep their link in PDF output
  (it was previously dropped by the heading restyling pass).

### Compatibility

- Documents without links, headings or typed metadata render
  byte-identically except for object numbering (page objects are now
  allocated before page dictionaries are built, to support forward
  destination references).
- The `/Info` dictionary no longer emits an empty `/Title ()` for
  untitled documents.

### Tests

- `PdfRendererLinksOutlineTest` (12 tests) — URI annotations, escaped
  URIs, rect sanity, GoTo destinations across pages, unknown-anchor
  fallback, outline tree shape, level clamping, `/Info` mapping.
- `MetadataRenderingTest` (8 tests) — HTML meta tags / lang attribute /
  escaping, Markdown frontmatter (typed + loose bag, YAML quoting).
- `FeatureSuiteTest` (16 tests) — strikethrough/highlight across the
  four renderers, drawn PDF decorations, TOC in PDF/HTML/Markdown/DOCX,
  watermark (ExtGState + rotation, HTML overlay), `openString` /
  `convertString`, `Format` enum round-trip.
- `DocxRunningElementsTest` (5 tests) — header/footer parts, rels,
  content types, PAGE/NUMPAGES fields, alignment and style mapping.
- Full suite: **824 tests / 2148 assertions**, all green. Generated
  PDFs validated externally with Ghostscript and `pdfinfo`; DOCX
  headers/footers validated through a LibreOffice PDF conversion.

---

## [0.8.3] — 2026-05-11

> **Bug fix patch (PDF).** Continuation pages created by automatic
> overflow (long paragraphs, tables, horizontal rules, images) now
> receive the same page chrome (background, header, footer) as the
> first page of each section.

### Fixed

- **Page chrome (background, header, footer) is now repainted on every
  physical page**, including pages created mid-paragraph by
  `PdfEngine::writeWrappedText()` auto-overflow and the
  needs-new-page paths inside `writeTable()`, `writeHorizontalRule()`
  and `writeImage()`. Previously a single long paragraph in a section
  with `PageSetup::backgroundColor` and a document-level footer would
  render the first page correctly but every continuation page came
  out blank-chromed (no background, no footer) — because those auto
  page breaks bypassed the `applyPageSetup` / `drawHeaderFooter`
  sequence wired only at the section-break level.

### Added

- **`PdfEngine::setOnNewPage(?Closure $hook)`** — registers a callback
  fired on every page started by the engine *except* the constructor's
  initial page. The hook runs after the cursor reset so
  `getCurrentPageNumber()` already returns the new page number and any
  drawing performed by the hook (e.g. background fill) lands at the
  head of the new page's content stream, guaranteeing correct z-order
  (background → header/footer → body).

- **Internal `PdfRenderer::paintPageChrome()`** — single source of truth
  for what every new physical page receives before body content
  (geometry + background + header/footer + trailing-line metric reset),
  driven by the engine `setOnNewPage` hook.

### Changed

- **`PdfRenderer::handlePageBreak()`** and `writeHorizontalRule()`'s
  internal break path no longer call `applyPageSetup` /
  `drawHeaderFooter` / reset `lastBlockLineHeight` explicitly — the
  engine's hook handles all three. Documents using the default 40pt
  gutter and no per-section overrides remain byte-identical; documents
  that previously suffered from the blank-continuation-page bug now
  paint chrome on every page.

- **`PdfRenderer::applyPageSetup(null)`** is now explicitly documented
  as a no-op (geometry untouched, no background fill). Header/footer
  is no longer this method's concern — it is applied separately by
  `paintPageChrome()` and therefore continues to render even on
  sections without a `PageSetup`.

### Added (tests)

- **`PdfRendererContinuationPagesTest`** — regression coverage for
  background/footer on multi-page overflow, `hideFooter` on continuations,
  vertical alignment overflow, and z-order per page stream.

---

## [0.8.2] — 2026-05-05

> **Bug fix patch.** A small but visible alignment bug in the PDF
> renderer: every absolute-positioned element (horizontal rules,
> tables, images, vertical-alignment fallback) was hardcoding `40pt`
> as the content area's left edge instead of reading the engine's
> actual `marginLeft`. Sections with a non-default gutter ended up
> with their rules / tables / images shifted left of the body text
> they should align with. No API break — purely a fix.

### Fixed

- **`PdfRenderer` no longer hardcodes 40pt as the content-area left
  edge.** Reported by a downstream user (Lumières, 18mm gutter ≈
  51pt): a centred `HorizontalRule` was being drawn 11pt to the left
  of the chapter title that followed it, because
  `writeHorizontalRule()` started its centring math at `x = 40` while
  `writeWrappedText()` correctly inherited `cursorX = marginLeft`.
  The same bug affected:

  - `writeHorizontalRule()` — start-x for left/center/right alignment.
  - `writeTable()` — `$startX` used to draw cell rectangles.
  - `writeImage()` — top-left of the image XObject placement.
  - `writeListItemLine()` — start-x of `<marker> <text>` lines.
  - `writeCodeBlock()` — start-x of code lines.
  - `writeTextRun()` — paragraph indent reference (the `0`-sentinel
    branch already worked because the engine's cursorX was correct;
    the bug only triggered when `indent > 0`, e.g. nested lists).
  - `writeSection()` — vertical-alignment fallback when no
    `PageSetup` is set; the bottom-of-content reference was 40 instead
    of `engine->getBottomMargin()`.

  All sites now read `$this->engine->getLeftMargin()` (or
  `getBottomMargin()` for the vertical-align fallback). Documents
  using the default 40pt gutter are byte-identical to v0.8.1.

### Added

- **`PdfEngine::getLeftMargin() / getRightMargin() / getTopMargin()`**
  — read-only getters mirroring the existing `getBottomMargin()`. The
  PDF renderer needs them now, but they're public so any
  consumer-side absolute positioning code (overlays, watermarks,
  custom decorations) can stop guessing where the content area
  actually starts.

### Tests

- `test_horizontal_rule_uses_engine_left_margin` — pins down the
  reported bug: a left-aligned rule on a section with
  `paddingLeft = 60` is drawn at `x = 60` (was `40`).
- `test_table_uses_engine_left_margin` — same guarantee for tables.
- `test_image_uses_engine_left_margin` — same guarantee for images.

---

## [0.8.1] — 2026-05-05

> **Bug fix patch.** A single corner-case fix in the
> `HorizontalRule` ↔ `reserveAscentFor()` interaction reported by
> a downstream user. No API change.

### Fixed

- **`writeHorizontalRule()` now feeds `lastBlockLineHeight` instead
  of zeroing it.** In v0.8.0, `writeHorizontalRule()` reset
  `lastBlockLineHeight = 0` at the end, which made the next
  paragraph's `reserveAscentFor()` short-circuit and skip ascent
  reservation. Symptom: a 28pt title placed immediately after a
  rule with `marginBottom = 6pt` (default) had its ascender
  (~20pt) punching through the rule line. The only workaround was
  to manually pad `marginBottom` to `~32pt` per case.

  The fix stores the rule's `marginBottom` as the trailing-line
  metric, so a following paragraph automatically gets
  `needed = max(0, ascender - marginBottom)` reserved before
  drawing — its top of glyphs lands exactly at the rule's bottom
  edge regardless of the rule's actual `marginBottom`. `writeBlock()`
  has been adjusted to NOT auto-reset `lastBlockLineHeight` after
  a `HorizontalRule` (paragraphs and rules now share that
  exemption).

  Practical effect: a default-margin rule followed by a big title
  now renders correctly without any margin tweaking.

### Tests

- New regression `test_horizontal_rule_marginBottom_protects_following_large_title`:
  asserts that the title's baseline Y is the same whether the rule's
  `marginBottom` is small (forcing reservation) or large (no
  reservation needed) — proof that the small-margin case correctly
  compensated.
- Full suite: **751 tests / 1816 assertions**, all green.

---

## [0.8.0] — 2026-05-05

> **Layout & typography APIs (additive).** Five fully additive APIs
> that round out the document model with the layout primitives that
> previously had to be hacked together at the application level
> (kludge paragraphs to push a frontispiece down, em-dashes that
> pretend to be a horizontal rule, manually space-padded letters to
> fake letter-spacing, …). Backwards-compatible : every existing
> document renders byte-identically when none of the new APIs are
> used.

### Added

- **A3 — Per-section running elements**. `Section::setHeader()`,
  `Section::setFooter()`, `Section::hideHeader()`, `Section::hideFooter()`
  let a section override (or suppress) the document-level
  `Document::setHeader()` / `setFooter()`. Typical use: a cover page
  or a full-bleed image page that should NOT carry the document's
  page-number footer (it would either disappear under the artwork
  or fight with the imagery for legibility). Resolution rule:
  hidden flag → no header ; explicit override → section's element ;
  otherwise → fallback to document's element. Honoured by both PDF
  and HTML renderers.
- **A4 — Section::setVerticalAlignment(VerticalAlignment::TOP|CENTER|BOTTOM)**.
  Controls vertical anchoring of the section's content within the
  page padding box. `TOP` (default) preserves previous behaviour ;
  `CENTER` and `BOTTOM` are useful for chapter openers, colophons
  or one-paragraph sections that should be visually balanced on
  the page. The PDF renderer captures the section's content slice,
  measures its height, then wraps it in a `q ... 1 0 0 1 0 dy cm
  ... Q` translation block — native PDF, no overhead, no
  pre-render measurement pass. The HTML renderer applies the same
  semantics via flexbox (`justify-content: center` / `flex-end`).
  Sections that overflow onto multiple pages safely fall back to
  TOP alignment to avoid a stale CTM bleeding across pages.
- **A4 — Per-side padding shortcuts on Section**. `setPagePaddingTop(float)`,
  `setPagePaddingRight(float)`, `setPagePaddingBottom(float)`,
  `setPagePaddingLeft(float)` for cases where a single side needs
  tweaking (e.g. push the frontispiece title down without touching
  left/right). The existing variadic `setPagePadding(...$values)`
  still accepts CSS-shorthand 1-/2-/3-/4-value forms and remains
  the recommended one-liner for the common cases.
- **B3 — ParagraphStyle::setFirstLineIndent(float)**. Mirrors the CSS
  `text-indent` property : only the FIRST wrapped line of the
  paragraph starts further to the right (or left, for negative
  values = hanging indent). The PDF wrap engine sees a tighter
  budget for the first line so wrapping accounts for the indent
  correctly. HTML emits `text-indent: Xpt` on the paragraph's
  inline style. Honoured by top-level paragraphs AND paragraphs
  inside a `TextZone`.
- **B4 — TextStyle::setLetterSpacing(float)**. Native character
  spacing in points. PDF emits the `Tc` (character spacing)
  operator before drawing the run and resets it to 0 after, so
  the advance is preserved by the PDF and copy-paste from the
  rendered file gives back the original (un-spaced) text — a
  property the previous app-level "insert thin spaces between
  every glyph" workarounds did not have. HTML emits `letter-spacing:
  Xpt` on the run's `<span>`. `measureTextWidth()` and `wrapText()`
  now both account for letter-spacing so wrapping stays correct.
  Combinable with letterSpacing-aware justify (Tc adds onto the
  spacing the justifier may already inject without producing
  double-counted advances).
- **B5 — `HorizontalRule` block element**. New first-class block
  element with fluent setters for `width` (absolute pt OR a
  percentage string like `'50%'`), `thickness`, `color`,
  `alignment` (LEFT/CENTER/RIGHT for partial-width rules) and
  per-side margins (`marginTop`, `marginBottom`). Renderers :
  - **PDF** : a stroked horizontal line on the current page,
    correctly clearing the trailing-line metric so the next
    paragraph isn't reservation-shifted against a non-existent
    baseline.
  - **HTML** : `<hr>` with inline CSS (`border-top:Wpt solid C ;
    width:X ; margin:T 0 B`).
  - **Markdown** : `---` thematic break (CommonMark).
  - **DOCX** : the canonical Word "horizontal line" — an empty
    paragraph carrying a `<w:pBdr><w:bottom .../></w:pBdr>` with
    the requested colour and thickness (in eighths-of-a-point).

  Convenience: `Section::addRule()` returns the new
  `HorizontalRule` instance for fluent chaining.

- **`Paperdoc\Enum\VerticalAlignment`** : `TOP` / `CENTER` / `BOTTOM`,
  used by `Section::setVerticalAlignment()`.
- **PdfEngine internals** :
  - `getPageContentLength()` and `wrapPageContentSince(int $offset,
    string $prefix, string $suffix)` for slicing arbitrary spans
    of the in-flight content stream and wrapping them in a `q ...
    Q` graphics-state block — currently used by section vertical
    alignment, future-proofed for any "translate / rotate this
    chunk" use case.
  - `drawColoredLine()` : `drawLine()` plus stroke-colour push and
    reset, used by `HorizontalRule`.
  - `measureTextWidth()` and `wrapText()` accept an optional
    `$letterSpacing` argument (in points).
  - `writeWrappedText()` and `writeWrappedTextAt()` accept new
    optional `$letterSpacing` and `$firstLineIndent` arguments
    (defaults preserve previous behaviour exactly).

### Changed

- `PdfRenderer` no longer caches the document-level header/footer
  in its own state ; instead it asks each `Section` to resolve
  its effective running elements, so per-section overrides /
  hide flags Just Work without a separate code path.
- `HtmlRenderer::renderSection()` wraps the body content in a
  `<div class="paperdoc-section-body">` ONLY when vertical
  alignment is non-TOP — top-aligned sections still produce the
  same DOM as 0.7.3 (no breaking change for downstream selectors).

### Compatibility

- **Backwards compatible.** Every existing document, test fixture
  and renderer call site renders byte-identically without any of
  the new APIs invoked.
- All defaults preserve previous behaviour: `firstLineIndent = 0`,
  `letterSpacing = 0`, `verticalAlignment = TOP`, no per-section
  header/footer override, no `HorizontalRule` magically inserted.

---

## [0.7.3] — 2026-05-04

> **Typography quality patch** — four fixes that materially improve
> the visual quality of PDF output, with no breaking API changes.
> Documents that rendered "almost right" before (slightly off-centre
> titles, baselines colliding between an "eyebrow" line and the
> title that follows it, justified lines with conspicuous "rivers"
> of whitespace) now look correct.

### Fixed

- **A1 — Vertical overlap between consecutive paragraphs of very
  different font sizes.** `PdfRenderer::writeParagraph()` now reserves
  enough vertical space before each new paragraph so the top of its
  glyphs (ascender) doesn't collide with the previous paragraph's
  baseline. Symptom that this addresses: a small "TABLE" eyebrow line
  followed by a 28pt "Sommaire" title visibly overlapping each other.
  The same logic is mirrored inside `writeTextZone()` so stacked
  paragraphs of different sizes within a single zone behave identically.
- **A2 — Imprecise text measurement.** `PdfEngine::measureTextWidth()`
  was using a single per-font average width (e.g. Times-Bold = 530/1000
  for every glyph), which made centring, right-alignment and
  word-wrapping noticeably off for titles full of narrow letters. The
  engine now ships per-glyph metric tables for the **14 standard PDF
  fonts** (Core 14: Helvetica, Times, Courier × {regular, bold,
  italic, bold-italic} + Symbol + ZapfDingbats), generated from the
  metric-compatible URW Base35 AFM files. As a side effect,
  `wrapText()` is also more precise — long lines wrap at the right word.
- **B1 — Justification with visible "rivers".** `emitTextLine()` now
  combines word-spacing (`Tw`) and character-spacing (`Tc`), with a
  per-line threshold: when the missing space would produce a
  word-spacing gap greater than ~3pt, the line silently falls back to
  flush-left rather than producing an unevenly-spaced "river". Last
  line of a paragraph stays left-aligned (unchanged).
- **C3 — Silent character drop.** `escapePdfString()` used
  `mb_convert_encoding()` with PHP's default of *silently dropping*
  any UTF-8 sequence not representable in WinAnsi (cp1252). Greek
  letters, mathematical symbols and CJK characters would simply
  disappear from the output, leaving wrong widths and unexpected
  word-spacing. The engine now substitutes a `?` for any such
  character so problems are visible in the output instead of haunting
  layout calculations.

### Added

- **`PdfEngine::getFontMetrics(string $fontName): array`** — returns
  ascender / descender / capHeight (in 1000em units) for any Core 14
  font. Used internally by the ascent reservation logic; exposed
  publicly so downstream renderers and tests can reproduce the same
  geometric reasoning.
- **`Paperdoc\Support\Pdf\Core14Widths`** — public-final class
  shipping the 14 width tables (256 ints each, indexed by WinAnsi byte
  code). Pure metric data (numbers), generated at build time from the
  AFM files; no font binaries shipped.
- **`tools/build-afm-widths.php`** — regeneration script for the
  width tables. Run it whenever the URW Base35 AFM files are updated
  upstream (extremely rare).

### Tests

- **+11 new regression tests** covering each of A1, A2, B1, C3 and
  the C1/C2 drawing-order contracts. **707 / 707** tests passing.
- New tests verify: 'WWWW' measures > 2.5× wider than 'iiii' in
  Helvetica-Bold; French typography glyphs (`é à è ç î ï « » œ`)
  measure within sane bounds; a small eyebrow followed by a 28pt
  title produces a baseline gap ≥ 21pt (= title ascender); a short
  justified line falls back to flush-left; the page background image
  is drawn before the running header/footer; the page background
  color is drawn before the background image.

### Compatibility

- Fully backward-compatible. Existing documents render with the same
  layout, only crisper (alignment is now where you asked it to be).
- The `CHAR_WIDTHS` per-font averages remain as a fallback for any
  custom font registered outside the Core 14.

---

## [0.7.2] — 2026-05-04

> **Bug fix** — `ParagraphStyle::alignment` was only honoured by
> `TextZone` paragraphs. Top-level paragraphs (and paragraphs inside
> blockquotes) silently rendered left-aligned in the PDF output even
> when set to `CENTER`, `RIGHT` or `JUSTIFY`. The HTML renderer was
> already correct.

### Fixed

- **`PdfRenderer::writeParagraph()`** now reads
  `ParagraphStyle::getAlignment()` and forwards it through
  `writeTextRun()` to the engine. Top-level Section paragraphs honour
  `LEFT` / `CENTER` / `RIGHT` / `JUSTIFY` exactly like `TextZone`
  paragraphs do.
- **`PdfRenderer::writeQuotedParagraph()`** propagates the alignment
  too, so blockquoted paragraphs with a non-default alignment render
  correctly.

### Changed

- **`PdfEngine::writeWrappedText()`** gained a `$align` parameter
  (default `'left'`, fully backward-compatible). Justified lines use
  the PDF word-spacing operator (`Tw`) — the last line stays
  left-aligned, like in `writeWrappedTextAt()`.
- The X origin of a wrapped block is now snapped at the start of the
  call, so a wrapped multi-line block keeps the same alignment box
  for every line.

### Tests

- New regression `test_paragraph_alignment_is_honored_at_section_root`
  in `PdfRendererTest`: renders the same sentence with each alignment
  and asserts that the `Td` X coordinates are strictly ordered
  (`LEFT < CENTER < RIGHT`) and that justified text emits a `Tw`
  operator. 696 / 696 tests passing.

---

## [0.7.1] — 2026-05-04

> **Page background sizing & per-paragraph text alignment** — the
> page background image now follows the same `cover` / `contain` /
> `auto` / `stretch` semantics as CSS, on both renderers, and
> `TextZone` paragraphs accept independent alignments
> (`left` / `center` / `right` / `justify`) — including a real
> word-spacing-based justification in the PDF output. Fully
> **non-breaking**: existing documents keep their previous look
> (`cover` was already the implicit behaviour).

### Added

- **`PageSetup::setBackgroundSize($value)`** — controls how the
  page background image is fitted. Four predefined modes are
  exposed as constants:
  - `BG_SIZE_COVER` (default) — fills the whole page while
    preserving the image's aspect ratio; overflow is clipped.
  - `BG_SIZE_CONTAIN` — fits inside the page while preserving the
    aspect ratio (may leave empty bands).
  - `BG_SIZE_AUTO` — image rendered at its natural size, centred,
    clipped if larger than the page.
  - `BG_SIZE_STRETCH` (alias of `'100% 100%'`) — fills the page
    without preserving the aspect ratio (legacy behaviour).
  Any other CSS-valid string (`'50% auto'`, `'300pt 200pt'`, …) is
  accepted verbatim in HTML.
- **`PageSetup::setBackgroundPosition($value)`** /
  **`PageSetup::setBackgroundRepeat($value)`** — full CSS
  parity for the background layer (defaults: `'center center'`,
  `'no-repeat'`).
- **`writeWrappedTextAt(... , string $align = 'left')`** in
  `PdfEngine` — supports `left`, `center`, `right` and `justify`.
  Justification distributes leftover horizontal space via the PDF
  word-spacing operator (`Tw`), and the last line of a paragraph is
  intentionally left-aligned to avoid stretched short lines.

### Changed

- **`HtmlRenderer` — TextZone clamp layout.** Each paragraph inside
  a `TextZone` now renders as its own `<div>` (instead of being
  joined with `<br>`), carrying its own `text-align`. This makes it
  possible to mix several alignments inside a single zone (e.g.
  centred title + justified body + right-aligned signature) while
  still respecting the line clamp / ellipsis behaviour.
- **`PdfRenderer::writeTextZone()`** now reads
  `ParagraphStyle::getAlignment()` and forwards it to the engine,
  giving `TextZone` the same alignment freedom in PDF output.
- **`PdfEngine::drawPageBackgroundImage()`** gained a `$size`
  parameter and reads the image's natural dimensions to compute the
  correct placement; `cover` and `auto` use a clip path to rein in
  the overflow.

### Notes

- Backward-compatible: existing code that didn't call any of the
  new setters keeps its previous rendering exactly (`cover` /
  `center center` / `no-repeat`).
- The string `'stretch'` is normalised to the CSS-valid
  `'100% 100%'` in HTML output, so users can pick whichever
  spelling they prefer.

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

[Unreleased]: https://github.com/paperdoc-dev/paperdoc-lib/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v1.0.0
[0.8.3]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.8.3
[0.8.2]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.8.2
[0.8.1]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.8.1
[0.8.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.8.0
[0.7.3]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.7.3
[0.7.2]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.7.2
[0.7.1]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.7.1
[0.7.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.7.0
[0.6.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.6.0
[0.5.0]: https://github.com/paperdoc-dev/paperdoc-lib/releases/tag/v0.5.0
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
