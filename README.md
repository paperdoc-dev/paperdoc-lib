# Paperdoc Library

[![Latest Version](https://img.shields.io/packagist/v/paperdoc-dev/paperdoc-lib.svg?style=flat-square)](https://packagist.org/packages/paperdoc-dev/paperdoc-lib)
[![Stable](https://img.shields.io/badge/stability-stable-brightgreen?style=flat-square)](https://github.com/paperdoc-dev/paperdoc-lib/releases)
[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue?style=flat-square)](https://www.php.net)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)
[![Tests](https://img.shields.io/github/actions/workflow/status/paperdoc-dev/paperdoc-lib/tests.yml?label=tests&style=flat-square)](https://github.com/paperdoc-dev/paperdoc-lib/actions)
[![PHPStan](https://img.shields.io/github/actions/workflow/status/paperdoc-dev/paperdoc-lib/tests.yml?label=phpstan&style=flat-square)](https://github.com/paperdoc-dev/paperdoc-lib/actions)

> A zero-dependency PHP library for generating, parsing and converting documents — PDF, HTML, CSV, DOCX, XLSX, PPTX, Markdown and more.

---

## Features

- **Generate** documents from scratch — PDF, HTML, CSV, DOCX, XLSX, PPTX, Markdown
- **Parse** existing documents into a unified in-memory model
- **Convert** between any supported format in one call
- **Rich document model** — headings, nested lists, bookmarks, code blocks, blockquotes, images, tables, page breaks, metadata
- **Layout & typography** — per-page setup, multi-column layout, backgrounds, text zones, headers/footers, footnotes, vertical alignment, first-line indent, letter spacing, horizontal rules
- **Native rendering** — block elements render cleanly to DOCX, PDF, HTML and Markdown
- **Hyperlinks** — parse from DOCX and round-trip to HTML, Markdown, DOCX and PDF (external URLs and internal anchors)
- **PDF outline & table of contents** — navigation panel and linked TOC across all formats
- **Watermark & rich text styles** — strikethrough, highlight, underline across formats
- **String I/O** — parse and convert content in memory, no file required
- **Typed Format enum** — use strings or `Paperdoc\Enum\Format` everywhere
- **Metadata** — title, author, subject, dates mapped to each format's native properties
- **Batch processing** — open and process multiple files at once
- **Laravel integration** — ServiceProvider and Facade included
- **AI-powered OCR** — post-correction and structured extraction via built-in HTTP providers (OpenAI, Anthropic, Gemini, Ollama)
- **Typed exceptions** — clear error hierarchy for parsers, renderers and LLM calls
- **Zero-dependency** — no third-party packages, no native binaries

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | ^8.2 |
| ext-dom | * |
| ext-mbstring | * |
| ext-zip | * |
| ext-zlib | * |

**Optional**

| Package | Version | Enables |
|---|---|---|
| illuminate/support | ^11.0 \| ^12.0 | Laravel integration (ServiceProvider, Facade) |
| ext-curl | * | Preferred HTTP transport for the built-in LLM providers (PHP streams fallback otherwise) |

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

Each section can declare its own page geometry through a `PageSetup`
value object, and place absolutely-positioned `TextZone` blocks anywhere
on the page. Combine several sections (each with its own `PageSetup`) to
build documents where every page has a different size and background.
Add a global `RunningElement` to the `Document` to draw a header/footer
on every page.

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
`setPageBackgroundImage()`, `setPageBackgroundColor()`,
`setColumnCount()`, `setColumnGap()`) that delegate to a lazily-created
`PageSetup`.

| Setter / Factory                                            | Purpose                                                |
|-------------------------------------------------------------|--------------------------------------------------------|
| `PageSetup::fromSize(PageSize, $orientation = 'portrait')`  | Use a standard format (A3/A4/A5/A6/Letter/Legal/Tabloid/Executive) |
| `PageSetup::custom($width, $height)`                        | Any dimensions in PDF points                           |
| `landscape()` / `portrait()`                                | Flip the active orientation                            |
| `setPadding(...)` (1–4 values)                              | CSS-style shorthand for top/right/bottom/left padding  |
| `setBackgroundColor($hex)`                                  | Solid full-bleed background color                      |
| `setBackgroundImage(Image)`                                 | Full-bleed image (on-disk or `Image::fromData()`)      |
| `setBackgroundSize(string)`                                 | `cover` (default), `contain`, `auto`, `stretch`, or any CSS string |
| `setBackgroundPosition(string)`                             | CSS string, default `'center center'`                  |
| `setBackgroundRepeat(string)`                               | CSS string, default `'no-repeat'`                      |
| `setColumnCount(int)` / `setColumnGap(float)`               | Multi-column body layout (PDF, HTML, DOCX)             |

### Multi-column layout

```php
$section->setPageSetup(
    PageSetup::fromSize(PageSize::A4)
        ->setColumnCount(2)
        ->setColumnGap(18.0) // points
);
```

Text flows down each column, then onto the next page. Tables and
full-width images still use the full content width.

### Footnotes

Attach a footnote to any `TextRun`. Markers (`[n]`) appear inline.
PDF places the note at the bottom of the page; HTML/Markdown list notes
at section end; DOCX appends a notes block at the end of the document.

```php
use Paperdoc\Document\Footnote;

$paragraph->addRun(TextRun::make(
    'See the specification',
    null,
    null,
    Footnote::make('ISO 32000-1:2008, §7.5'),
));
```

### Background image fit

PDF and HTML share the same modes. `cover` and `auto` clip overflow.

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

#### Per-paragraph alignment

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

Since **v1.0.0**, the **DOCX renderer** honours document-level running
elements too: it writes native `header1.xml` / `footer1.xml` parts and
emits `{page}` / `{pages}` as live Word `PAGE` / `NUMPAGES` fields, so
page numbers stay correct when the document is edited in Word.
(Per-section overrides below remain PDF/HTML-only — Word's section
model differs.)

The HTML renderer adds a translucent `rgba(255, 255, 255, 0.85)`
backdrop with a `backdrop-filter: blur(2px)` behind the running
elements so they remain legible on top of any background image. The
library does not automatically reserve vertical space for the
header/footer — keep that in mind when positioning a `TextZone` close
to a page edge.

### Per-section header / footer override

A document-level header/footer applies uniformly to every page by
default. Often that's not what you want — a cover page should NOT
carry the page-number footer (it would either disappear under the
artwork or fight with the imagery for legibility), and a colophon
on the last page might want a different label. Sections can override
or suppress the document-level running elements:

```php
use Paperdoc\Document\Style\RunningElement;

// Document-level: every page gets this footer by default.
$doc->setFooter(RunningElement::make('Page {page} / {pages}'));

// Cover page: NO footer at all.
$cover = $doc->openSection('cover')->hideFooter();

// Body pages: inherit the document footer.
$body = $doc->openSection('body');
// (nothing to do — automatic fallback)

// Colophon: per-section override.
$colophon = $doc->openSection('colophon')
    ->setFooter(RunningElement::make('— Fin —'));
```

Resolution rule — for every page, the renderer picks (in order) :

1. If `Section::hideHeader()` / `hideFooter()` was called → no
   header/footer is drawn.
2. If `Section::setHeader()` / `setFooter()` was called with a
   non-null element → that element is drawn.
3. Otherwise → the document-level header/footer is drawn (if any).

Both PDF and HTML renderers honour this resolution.

### Vertical alignment

By default the content of a section flows from the top padding
downwards. For pages that should breathe vertically — chapter
openers, colophons, frontispieces, single-paragraph "blank" pages —
you can centre or bottom-anchor the content :

```php
use Paperdoc\Enum\VerticalAlignment;

$opener = $doc->openSection('chapter-1-opener')
    ->setPageSize(PageSize::A5)
    ->setVerticalAlignment(VerticalAlignment::CENTER);
$opener->addText('CHAPITRE 1', TextStyle::make()->setFontSize(10)->setColor('#888'));
$opener->addText('Le Signal sur le Balcon', TextStyle::make()->setFontSize(28)->setBold());

$colophon = $doc->openSection('colophon')
    ->setVerticalAlignment(VerticalAlignment::BOTTOM);
$colophon->addText('© 2026 — All rights reserved.');
```

Implementation notes :

- The PDF renderer captures the section's content slice, measures
  its rendered height, then wraps it in a native PDF `q ... 1 0 0 1
  0 dy cm ... Q` translation block — no overhead, no pre-render
  measurement pass.
- The HTML renderer applies the same semantics via flexbox
  (`display:flex; justify-content:center` for `CENTER`, `flex-end`
  for `BOTTOM`).
- Sections that overflow onto a second page automatically fall
  back to TOP alignment to avoid a stale CTM bleeding across
  pages. If you need centring on a section with lots of content,
  reduce its content first.

### Per-side padding shortcuts

`Section::setPagePadding(...$values)` already accepts CSS-shorthand
1-/2-/3-/4-value forms. When only ONE side needs tweaking — typical
for a frontispiece title that should sit ~110pt from the top — the
new per-side shortcuts are clearer :

```php
$frontispiece = $doc->openSection('frontispiece')
    ->setPageSize(PageSize::A5)
    ->setPagePaddingTop(110.0)
    ->setPagePaddingBottom(60.0);
$frontispiece->addText('La Lumière des Autres', TextStyle::make()->setFontSize(32)->setBold());
```

Available : `setPagePaddingTop()`, `setPagePaddingRight()`,
`setPagePaddingBottom()`, `setPagePaddingLeft()`. They all return
`$this` for chaining.

---

## First-line indent and letter-spacing

Two style-level additions that previously had to be hacked at the
application layer :

### Paragraph first-line indent

`ParagraphStyle::setFirstLineIndent(float $points)` mirrors the CSS
`text-indent` property : only the first line of the paragraph
starts further to the right. Negative values produce a hanging
indent (first line jutting OUT to the left of the block — a
typographic device used in lists or dictionary entries).

```php
use Paperdoc\Document\Style\ParagraphStyle;

$body = ParagraphStyle::make()
    ->setLineSpacing(1.4)
    ->setFirstLineIndent(18.0); // ~6mm — classic book body indent

$paragraph->setStyle($body);
```

The PDF wrap engine sees a tighter budget for the first line so
wrapping accounts for the indent correctly. The HTML renderer
emits `text-indent: Xpt` on the paragraph's inline style.

### Run letter-spacing

`TextStyle::setLetterSpacing(float $points)` opens out a run of
text by the requested number of points between every pair of
adjacent glyphs. Common uses : an opened-out section heading
(`setLetterSpacing(1.5)`), or marking an acronym in small-caps
style. Negative values pull glyphs closer.

```php
use Paperdoc\Document\Style\TextStyle;

$eyebrow = TextStyle::make()
    ->setFontSize(10)
    ->setBold()
    ->setColor('#888')
    ->setLetterSpacing(2.0); // wide tracking for the eyebrow

$paragraph->addRun(new TextRun('CHAPITRE PREMIER', $eyebrow));
```

PDF emits the native `Tc` operator and resets to 0 after the run,
so copy-paste from the rendered file gives back the original
(un-spaced) text — a property the previous "insert thin spaces
between every glyph" workarounds did not have. HTML emits
`letter-spacing: Xpt`. `measureTextWidth()` and `wrapText()`
correctly account for letter-spacing.

---

## Horizontal rule

A first-class block element for visual separators :

```php
use Paperdoc\Document\HorizontalRule;
use Paperdoc\Enum\Alignment;

// Quick: full-width default-styled rule.
$section->addRule();

// Customised: 50%-width centred grey hairline.
$section->addRule()
    ->setWidth('50%')
    ->setThickness(0.75)
    ->setColor('#aaaaaa')
    ->setAlignment(Alignment::CENTER)
    ->setMargins(8.0, 12.0);

// Pure absolute pt width.
$section->addRule()->setWidth(140.0)->setColor('#1F3763');
```

| Property      | Type           | Default     | Notes                                                |
|---------------|----------------|-------------|------------------------------------------------------|
| `width`       | `string\|float` | `'100%'`    | Either a CSS-style percentage (`'50%'`) or absolute pt. |
| `thickness`   | `float` (pt)    | `0.5`       | Word-style "thin rule" by default.                   |
| `color`       | `string` hex   | `'#999999'` | CSS hex (`#rgb` or `#rrggbb`).                       |
| `alignment`   | `Alignment`    | `CENTER`    | LEFT / CENTER / RIGHT for partial-width rules.       |
| `marginTop`   | `float` (pt)    | `6.0`       | Vertical breathing space above the rule.             |
| `marginBottom`| `float` (pt)    | `6.0`       | Below.                                               |

Renderers :
- **PDF** : a stroked horizontal line, drawn with the requested
  thickness and stroke colour.
- **HTML** : `<hr>` with inline CSS (`border-top:Wpt solid C ;
  width:X ; margin:T 0 B`).
- **Markdown** : `---` thematic break (CommonMark).
- **DOCX** : the canonical Word "horizontal line" — an empty
  paragraph carrying a `<w:pBdr><w:bottom .../></w:pBdr>` with
  the requested colour and thickness in eighths-of-a-point.

---

## Rendering

Every element of the document model is natively rendered by **all four** core renderers — no element is silently dropped, every output is a valid file format.

| Element            | DOCX                                                    | PDF                                            | HTML                                  | Markdown                          |
|--------------------|---------------------------------------------------------|------------------------------------------------|---------------------------------------|-----------------------------------|
| `Heading` (1–6)    | `<w:pStyle w:val="HeadingN"/>` + bookmark anchor        | typed font sizes (24/20/16/14/13/12 pt) + navy | `<h1>`…`<h6>` with `id`               | `#`…`######`, optional `{#id}`    |
| `Paragraph`        | `<w:p>` + run styling                                   | wrapped text + inline run styles               | `<p>` + inline `<span>`               | plain text + emphasis             |
| `ListBlock`        | `<w:numPr>` + `word/numbering.xml`, nested `<w:ilvl>`   | `•` / `1.` markers, depth-based indent         | `<ul>` / `<ol start="N">`, nested     | `-` / `1.`, two-space indent      |
| `Blockquote`       | `<w:pStyle w:val="Quote"/>` + indent                    | indented italic muted-grey                     | `<blockquote>` (nested children)      | `> ` prefixed lines               |
| `CodeBlock`        | `<w:pStyle w:val="Code"/>` + Consolas + `<w:br/>`       | Courier, dedicated spacing                     | `<pre><code class="language-…">`      | fenced ` ```lang ` block          |
| `Bookmark`         | `<w:bookmarkStart/>` / `<w:bookmarkEnd/>`               | named destination — internal link target *(v1.0.0)* | `<a id="…" class="paperdoc-bookmark">`| inline `<a id="…"></a>`           |
| `TextLink`         | `<w:hyperlink>` (external rels + `w:anchor` + tooltip)  | clickable Link annotation (URI / GoTo) *(v1.0.0)* | `<a href>` with safe `target/rel`     | safe `[label](url "title")`       |
| `Image`            | `<w:drawing>` + `word/media/imageN.ext` rel             | XObject DCT (JPEG/PNG/GIF via GD re-encode)    | `<img src>` or `data:` URI            | `![alt](path)` or `data:` URI     |
| `Table`            | `<w:tbl>` with header rows + `gridSpan`                 | drawn cells with header bg                     | `<table>` + striped rows              | `\|` rows                         |
| `PageBreak`        | `<w:br w:type="page"/>`                                 | `newPage()`                                    | `.page-break` divider                 | blank line                        |
| `HorizontalRule` | bordered empty `<w:p>` (`<w:pBdr>`)            | stroked PDF line                               | `<hr>` with inline CSS                | `---` thematic break              |
| `Metadata`         | `docProps/core.xml`                                     | full `/Info` dict *(v1.0.0)*                   | `<head>` meta + `lang` *(v1.0.0)*     | YAML frontmatter *(v1.0.0)*       |
| `Heading` → outline | (outline levels via `pStyle`)                          | `/Outlines` bookmarks panel *(v1.0.0)*         | `id` anchors                          | `{#id}` anchors                   |

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
| `Paperdoc\Exceptions\LlmException` | An LLM provider request fails (transport, HTTP error, unusable response) |

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

Every `TextRun` can carry an optional `Paperdoc\Document\Link\TextLink`. Links survive the full round-trip: they're parsed from DOCX (`<w:hyperlink>`) and rendered natively by the HTML, Markdown, DOCX **and PDF** renderers.

### Clickable links in PDF (v1.0.0)

The native PDF renderer emits real Link annotations:

- **External URLs** become `/A << /S /URI >>` actions — clicking opens the browser.
- **Internal anchors** (`TextLink::make('', 'my-anchor')`) become GoTo destinations pointing at the page and position of the matching `Bookmark` or `Heading` id. Forward references work: a table of contents on page 1 can target a heading on page 12.
- Wrapped links get one clickable rectangle per line; links crossing an automatic page break attach each rectangle to its own page. Links inside `TextZone` blocks are clickable too.
- A link to an anchor that is never declared renders as styled text without an annotation (nothing breaks).

```php
$section->addText('See chapter 2', null, TextLink::make('', 'chap-2'));
// … later, possibly pages away:
$section->addElement(Heading::make('Chapter 2', 1, 'chap-2'));
```

Headings also feed the **PDF outline** (the "bookmarks" side panel): every `Heading` becomes a navigable outline entry, nested by level, and the document opens with the panel visible (`/PageMode /UseOutlines`).

---

## Table of contents, watermark & rich styles (v1.0.0)

```php
use Paperdoc\Document\Style\{TextStyle, Watermark};
use Paperdoc\Enum\Format;
use Paperdoc\Support\DocumentManager;

$doc = DocumentManager::create(Format::PDF, 'Rapport');
$doc->setWatermark(Watermark::make('CONFIDENTIEL')->setOpacity(0.2)->setAngle(-45));

$section = $doc->openSection();
$section->addTableOfContents(maxLevel: 3, title: 'Sommaire'); // clickable in PDF/HTML/MD, native field in DOCX
$section->addElement(\Paperdoc\Document\Heading::make('Introduction', 1));

$section->addText('ancien prix', TextStyle::make()->setStrikethrough());
$section->addText('à retenir', TextStyle::make()->setHighlight('#FFF3B0'));
```

```php
// Parse & convert without touching the filesystem:
$html = DocumentManager::convertString("# Titre\n\nCorps.", 'md', 'html');
$doc  = DocumentManager::openString($csvContent, Format::CSV);
```

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

## Static Analysis

```bash
composer phpstan
```

PHPStan runs at `level: max` on `src/`. The initial adoption uses `phpstan-baseline.neon` to freeze the current legacy debt so new type regressions fail CI immediately.

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
├── Llm/               # Native AI/LLM layer (built-in OpenAI/Anthropic/Gemini/Ollama HTTP providers)
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

Thanks to everyone who has contributed to **paperdoc-lib**. Full details: [CONTRIBUTORS.md](CONTRIBUTORS.md).

**Maintainers**

- **Paperdoc team** — [@paperdoc-dev](https://github.com/paperdoc-dev)

**Community**

- **Olivier Mourlevat** — [@olivM](https://github.com/olivM)
  - DOCX hyperlink parsing and HTML/Markdown hyperlink rendering ([#4](https://github.com/paperdoc-dev/paperdoc-lib/pull/4))
  - DOCX bullet/numbered list parsing and Markdown list rendering ([#7](https://github.com/paperdoc-dev/paperdoc-lib/pull/7))
- **Bogdan Hossu** — [@BogdanHossu](https://github.com/BogdanHossu)
  - PDF CMap glyph code string cast fix ([#8](https://github.com/paperdoc-dev/paperdoc-lib/pull/8))

Want to be listed? Open a PR that gets merged into `main` — see [CONTRIBUTING.md](CONTRIBUTING.md).

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for release history.

---

## License

Paperdoc Library is released under the **[MIT License](LICENSE)** — free to use, modify and distribute, commercial or not.

© Paperdoc — [paperdoc.dev](https://paperdoc.dev)
