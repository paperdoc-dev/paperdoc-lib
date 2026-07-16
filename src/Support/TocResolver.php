<?php

declare(strict_types=1);

namespace Paperdoc\Support;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\{Blockquote, Heading, ListBlock};

/**
 * Walks a document once, assigns every Heading a stable anchor
 * (explicit id, or a generated one) and exposes the flat entry list
 * used by the TableOfContents renderers.
 */
final class TocResolver
{
    /** @var array<int, string> spl_object_id(Heading) => anchor */
    private array $anchors = [];

    /** @var list<array{level: int, text: string, anchor: string, generated: bool}> */
    private array $entries = [];

    public function __construct(DocumentInterface $document)
    {
        foreach ($document->getSections() as $section) {
            $this->walk($section->getElements());
        }
    }

    public function anchorFor(Heading $heading): string
    {
        return $this->anchors[spl_object_id($heading)]
            ?? ($heading->hasId() ? $heading->getId() : '');
    }

    /** @return list<array{level: int, text: string, anchor: string, generated: bool}> */
    public function entries(int $maxLevel = 6): array
    {
        return array_values(array_filter(
            $this->entries,
            static fn (array $e): bool => $e['level'] <= $maxLevel,
        ));
    }

    public function hasEntries(): bool
    {
        return $this->entries !== [];
    }

    /** @param iterable<mixed> $elements */
    private function walk(iterable $elements): void
    {
        foreach ($elements as $element) {
            if ($element instanceof Heading) {
                $anchor = $element->hasId()
                    ? $element->getId()
                    : 'paperdoc-h' . (count($this->entries) + 1);

                $this->anchors[spl_object_id($element)] = $anchor;
                $this->entries[] = [
                    'level'     => max(1, min(6, $element->getLevel())),
                    'text'      => $element->getPlainText(),
                    'anchor'    => $anchor,
                    'generated' => ! $element->hasId(),
                ];
            } elseif ($element instanceof ListBlock) {
                foreach ($element->getItems() as $item) {
                    $this->walk($item->getBlocks());
                }
            } elseif ($element instanceof Blockquote) {
                $this->walk($element->getElements());
            }
        }
    }
}
