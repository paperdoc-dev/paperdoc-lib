<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Exceptions\InvalidDocumentException;

/**
 * Named landmark in a document — the target of an internal link
 * (`TextLink` with a matching `anchor`). Invisible at render time; only
 * its `id` matters.
 *
 * Bookmarks are modelled as block-level elements for simplicity. Placing
 * one between two paragraphs is enough for the vast majority of use
 * cases. Finer-grained inline bookmarks may be introduced in a future
 * version if needed.
 */
final class Bookmark implements BlockElementInterface, \JsonSerializable
{
    public function __construct(private string $id)
    {
        $this->assertValidId($id);
    }

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $id): static
    {
        return new static($id);
    }

    /* -------------------------------------------------------------
     | DocumentElementInterface
     |------------------------------------------------------------- */

    public function getType(): string { return 'bookmark'; }

    /* -------------------------------------------------------------
     | Id
     |------------------------------------------------------------- */

    public function getId(): string { return $this->id; }

    public function setId(string $id): static
    {
        $this->assertValidId($id);
        $this->id = $id;

        return $this;
    }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'bookmark',
            'id'   => $this->id,
        ];
    }

    /* -------------------------------------------------------------
     | Internal
     |------------------------------------------------------------- */

    private function assertValidId(string $id): void
    {
        if (trim($id) === '') {
            throw new InvalidDocumentException('Bookmark id cannot be empty.');
        }
    }
}
