<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Exceptions\InvalidDocumentException;

/**
 * First-class heading element (levels 1–6). Unlike the legacy pattern of
 * a Paragraph with a ParagraphStyle::headingLevel, a Heading is typed
 * natively and supports:
 *
 *  - styled runs (bold mid-title, links, etc.)
 *  - an optional `id` used as a bookmark target by hyperlinks and TOCs
 */
class Heading implements BlockElementInterface, \JsonSerializable
{
    public const MIN_LEVEL = 1;
    public const MAX_LEVEL = 6;

    /** @var TextRun[] */
    private array $runs = [];

    public function __construct(
        private int $level = 1,
        private string $id = '',
    ) {
        $this->assertValidLevel($level);
    }

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $text = '', int $level = 1, string $id = '', ?TextStyle $style = null): static
    {
        $heading = new static($level, $id);

        if ($text !== '' || $style !== null) {
            $heading->addRun(new TextRun($text, $style));
        }

        return $heading;
    }

    /* -------------------------------------------------------------
     | DocumentElementInterface
     |------------------------------------------------------------- */

    public function getType(): string { return 'heading'; }

    /* -------------------------------------------------------------
     | Level
     |------------------------------------------------------------- */

    public function getLevel(): int { return $this->level; }

    public function setLevel(int $level): static
    {
        $this->assertValidLevel($level);
        $this->level = $level;

        return $this;
    }

    /* -------------------------------------------------------------
     | Id (anchor target for #-style links)
     |------------------------------------------------------------- */

    public function getId(): string { return $this->id; }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function hasId(): bool { return $this->id !== ''; }

    /* -------------------------------------------------------------
     | Runs
     |------------------------------------------------------------- */

    public function addRun(TextRun $run): static
    {
        $this->runs[] = $run;

        return $this;
    }

    public function addText(string $text, ?TextStyle $style = null, ?TextLink $link = null): static
    {
        return $this->addRun(new TextRun($text, $style, $link));
    }

    /** @return TextRun[] */
    public function getRuns(): array { return $this->runs; }

    public function getPlainText(): string
    {
        return implode('', array_map(fn (TextRun $r) => $r->getText(), $this->runs));
    }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        $result = [
            'type'  => 'heading',
            'level' => $this->level,
            'text'  => $this->getPlainText(),
            'runs'  => $this->runs,
        ];

        if ($this->id !== '') {
            $result['id'] = $this->id;
        }

        return $result;
    }

    /* -------------------------------------------------------------
     | Internal
     |------------------------------------------------------------- */

    private function assertValidLevel(int $level): void
    {
        if ($level < self::MIN_LEVEL || $level > self::MAX_LEVEL) {
            throw new InvalidDocumentException(sprintf(
                'Invalid heading level %d; expected between %d and %d.',
                $level,
                self::MIN_LEVEL,
                self::MAX_LEVEL,
            ));
        }
    }
}
