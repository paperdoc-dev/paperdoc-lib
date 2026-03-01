<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use Paperdoc\Contracts\ParserInterface;
use Paperdoc\Document\{Image, PageBreak, Paragraph, Table};
use Paperdoc\Parsers\MarkdownParser;

class MarkdownParserTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/paperdoc_md_parse_' . uniqid();
        mkdir($this->tmpDir, 0755, true);
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->tmpDir . '/*'));
        @rmdir($this->tmpDir);
    }

    private function writeMd(string $content, string $name = 'test.md'): string
    {
        $path = $this->tmpDir . '/' . $name;
        file_put_contents($path, $content);

        return $path;
    }

    public function test_implements_parser_interface(): void
    {
        $this->assertInstanceOf(ParserInterface::class, new MarkdownParser());
    }

    public function test_supports_md_extensions(): void
    {
        $parser = new MarkdownParser();

        $this->assertTrue($parser->supports('md'));
        $this->assertTrue($parser->supports('markdown'));
        $this->assertTrue($parser->supports('mkd'));
        $this->assertTrue($parser->supports('mdown'));
        $this->assertTrue($parser->supports('MD'));
        $this->assertFalse($parser->supports('html'));
        $this->assertFalse($parser->supports('pdf'));
    }

    public function test_parse_format_is_md(): void
    {
        $path = $this->writeMd('Hello');
        $doc = (new MarkdownParser())->parse($path);

        $this->assertSame('md', $doc->getFormat());
    }

    public function test_parse_atx_headings(): void
    {
        $md = <<<'MD'
# Heading 1

## Heading 2

### Heading 3

#### Heading 4
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertGreaterThanOrEqual(4, count($paragraphs));
        $this->assertSame('Heading 1', $paragraphs[0]->getPlainText());
        $this->assertSame('Heading 2', $paragraphs[1]->getPlainText());
        $this->assertSame('Heading 3', $paragraphs[2]->getPlainText());
        $this->assertSame('Heading 4', $paragraphs[3]->getPlainText());
    }

    public function test_parse_setext_headings(): void
    {
        $md = <<<'MD'
Title H1
========

Title H2
--------
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertGreaterThanOrEqual(2, count($paragraphs));
        $this->assertSame('Title H1', $paragraphs[0]->getPlainText());
        $this->assertSame('Title H2', $paragraphs[1]->getPlainText());
    }

    public function test_parse_simple_paragraphs(): void
    {
        $md = <<<'MD'
Premier paragraphe de texte.

Deuxième paragraphe de texte.
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertCount(2, $paragraphs);
        $this->assertSame('Premier paragraphe de texte.', $paragraphs[0]->getPlainText());
        $this->assertSame('Deuxième paragraphe de texte.', $paragraphs[1]->getPlainText());
    }

    public function test_parse_bold_text(): void
    {
        $path = $this->writeMd('Voici du **texte gras** ici.');
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $runs = $paragraphs[0]->getRuns();

        $this->assertSame('Voici du ', $runs[0]->getText());
        $this->assertTrue($runs[0]->getStyle() === null || ! $runs[0]->getStyle()->isBold());

        $this->assertSame('texte gras', $runs[1]->getText());
        $this->assertTrue($runs[1]->getStyle()->isBold());

        $this->assertSame(' ici.', $runs[2]->getText());
    }

    public function test_parse_italic_text(): void
    {
        $path = $this->writeMd('Du *texte italique* ici.');
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $runs = $paragraphs[0]->getRuns();

        $this->assertSame('texte italique', $runs[1]->getText());
        $this->assertTrue($runs[1]->getStyle()->isItalic());
    }

    public function test_parse_bold_italic_text(): void
    {
        $path = $this->writeMd('Du ***texte fort*** ici.');
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $runs = $paragraphs[0]->getRuns();

        $this->assertSame('texte fort', $runs[1]->getText());
        $this->assertTrue($runs[1]->getStyle()->isBold());
        $this->assertTrue($runs[1]->getStyle()->isItalic());
    }

    public function test_parse_inline_code(): void
    {
        $path = $this->writeMd('Utilise `echo $var` ici.');
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $runs = $paragraphs[0]->getRuns();

        $this->assertSame('echo $var', $runs[1]->getText());
        $this->assertSame('Courier', $runs[1]->getStyle()->getFontFamily());
    }

    public function test_parse_link(): void
    {
        $path = $this->writeMd('Voir [mon site](https://example.com) pour plus.');
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $runs = $paragraphs[0]->getRuns();

        $this->assertSame('mon site', $runs[1]->getText());
        $this->assertTrue($runs[1]->getStyle()->isUnderline());
        $this->assertSame('#2563EB', $runs[1]->getStyle()->getColor());
    }

    public function test_parse_image(): void
    {
        $path = $this->writeMd('![Logo](/images/logo.png)');
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $images = array_values(array_filter($elements, fn ($e) => $e instanceof Image));
        $this->assertCount(1, $images);
        $this->assertSame('/images/logo.png', $images[0]->getSrc());
        $this->assertSame('Logo', $images[0]->getAlt());
    }

    public function test_parse_gfm_table(): void
    {
        $md = <<<'MD'
| Nom   | Age |
|-------|-----|
| Alice | 30  |
| Bob   | 25  |
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));
        $this->assertCount(1, $tables);

        $rows = $tables[0]->getRows();
        $this->assertCount(3, $rows);
        $this->assertTrue($rows[0]->isHeader());
        $this->assertFalse($rows[1]->isHeader());

        $this->assertStringContainsString('Nom', $rows[0]->getCells()[0]->getPlainText());
        $this->assertStringContainsString('Alice', $rows[1]->getCells()[0]->getPlainText());
        $this->assertStringContainsString('Bob', $rows[2]->getCells()[0]->getPlainText());
    }

    public function test_parse_bullet_list(): void
    {
        $md = <<<'MD'
- Premier
- Deuxième
- Troisième
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertCount(3, $paragraphs);
        $this->assertStringContainsString('Premier', $paragraphs[0]->getPlainText());
        $this->assertStringContainsString('Deuxième', $paragraphs[1]->getPlainText());
    }

    public function test_parse_numbered_list(): void
    {
        $md = <<<'MD'
1. Premier
2. Deuxième
3. Troisième
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertCount(3, $paragraphs);
        $this->assertStringContainsString('Premier', $paragraphs[0]->getPlainText());
    }

    public function test_parse_blockquote(): void
    {
        $md = <<<'MD'
> Ceci est une citation importante.
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertCount(1, $paragraphs);
        $this->assertStringContainsString('citation importante', $paragraphs[0]->getPlainText());
        $this->assertTrue($paragraphs[0]->getRuns()[0]->getStyle()->isItalic());
    }

    public function test_parse_fenced_code_block(): void
    {
        $md = <<<'MD'
```php
echo "Hello";
$x = 42;
```
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertCount(1, $paragraphs);
        $this->assertStringContainsString('echo "Hello"', $paragraphs[0]->getPlainText());
        $this->assertSame('Courier', $paragraphs[0]->getRuns()[0]->getStyle()->getFontFamily());
    }

    public function test_parse_horizontal_rule(): void
    {
        $md = <<<'MD'
Avant

---

Après
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $pageBreaks = array_values(array_filter($elements, fn ($e) => $e instanceof PageBreak));
        $this->assertCount(1, $pageBreaks);
    }

    public function test_parse_front_matter(): void
    {
        $md = <<<'MD'
---
title: Mon Document
author: Jean Dupont
date: 2025-01-15
---

# Contenu principal
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);

        $this->assertSame('Mon Document', $doc->getTitle());
        $this->assertSame('Jean Dupont', $doc->getMetadata()['author'] ?? null);
        $this->assertSame('2025-01-15', $doc->getMetadata()['date'] ?? null);
    }

    public function test_parse_front_matter_with_quotes(): void
    {
        $md = <<<'MD'
---
title: "Titre avec espaces"
author: 'Auteur'
---

Texte.
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);

        $this->assertSame('Titre avec espaces', $doc->getTitle());
        $this->assertSame('Auteur', $doc->getMetadata()['author'] ?? null);
    }

    public function test_title_fallback_to_filename(): void
    {
        $path = $this->writeMd('Simple text.', 'readme.md');
        $doc = (new MarkdownParser())->parse($path);

        $this->assertSame('readme', $doc->getTitle());
    }

    public function test_parse_nonexistent_file_throws(): void
    {
        $this->expectException(\RuntimeException::class);

        (new MarkdownParser())->parse('/nonexistent/file.md');
    }

    public function test_parse_complex_document(): void
    {
        $md = <<<'MD'
---
title: Guide technique
author: Équipe dev
---

# Introduction

Ce document décrit l'architecture du projet.

## Stack technique

Nous utilisons les technologies suivantes :

- **PHP 8.2** comme langage principal
- *Laravel 11* comme framework
- `Redis` pour le cache

## Tableau comparatif

| Technologie | Usage         |
|-------------|---------------|
| PHP         | Backend       |
| Vue.js      | Frontend      |
| Redis       | Cache/Session |

## Code exemple

```php
$app = new Application();
$app->run();
```

> Ce guide est mis à jour régulièrement.

---

## Conclusion

Pour plus d'informations, voir [la doc](https://docs.example.com).
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);

        $this->assertSame('Guide technique', $doc->getTitle());
        $this->assertSame('Équipe dev', $doc->getMetadata()['author'] ?? null);

        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertGreaterThan(5, count($paragraphs));

        $tables = array_values(array_filter($elements, fn ($e) => $e instanceof Table));
        $this->assertCount(1, $tables);

        $pageBreaks = array_values(array_filter($elements, fn ($e) => $e instanceof PageBreak));
        $this->assertCount(1, $pageBreaks);
    }

    public function test_parse_underscore_bold(): void
    {
        $path = $this->writeMd('Du __texte gras__ ici.');
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();
        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $runs = $paragraphs[0]->getRuns();

        $this->assertSame('texte gras', $runs[1]->getText());
        $this->assertTrue($runs[1]->getStyle()->isBold());
    }

    public function test_multi_line_paragraph(): void
    {
        $md = <<<'MD'
Première ligne
suite de la même ligne
encore la suite.

Autre paragraphe.
MD;

        $path = $this->writeMd($md);
        $doc = (new MarkdownParser())->parse($path);
        $elements = $doc->getSections()[0]->getElements();

        $paragraphs = array_values(array_filter($elements, fn ($e) => $e instanceof Paragraph));
        $this->assertCount(2, $paragraphs);
        $this->assertStringContainsString('Première ligne', $paragraphs[0]->getPlainText());
        $this->assertStringContainsString('suite de la même ligne', $paragraphs[0]->getPlainText());
    }
}
