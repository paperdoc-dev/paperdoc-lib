<?php

declare(strict_types=1);

namespace Paperdoc\Document;

/**
 * Typed document properties — the standardised "author / subject /
 * keywords / created / modified / language" payload found in
 * `docProps/core.xml` (DOCX/XLSX/PPTX), XMP metadata (PDF) and
 * `<meta>` tags (HTML).
 *
 * Kept deliberately separate from `Document::getMetadata()` (a loose
 * key/value bag used for library-internal extras like `source_file`).
 */
class Metadata implements \JsonSerializable
{
    public function __construct(
        private string $author = '',
        private string $subject = '',
        private string $description = '',
        private string $keywords = '',
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $modifiedAt = null,
        private string $language = '',
    ) {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(): static
    {
        return new static();
    }

    /**
     * @param array{
     *     author?: string,
     *     subject?: string,
     *     description?: string,
     *     keywords?: string,
     *     createdAt?: \DateTimeInterface|string|null,
     *     modifiedAt?: \DateTimeInterface|string|null,
     *     language?: string,
     * } $data
     */
    public static function fromArray(array $data): static
    {
        return new static(
            author:      (string) ($data['author']      ?? ''),
            subject:     (string) ($data['subject']     ?? ''),
            description: (string) ($data['description'] ?? ''),
            keywords:    (string) ($data['keywords']    ?? ''),
            createdAt:   self::toDateTime($data['createdAt']  ?? null),
            modifiedAt:  self::toDateTime($data['modifiedAt'] ?? null),
            language:    (string) ($data['language']    ?? ''),
        );
    }

    /* -------------------------------------------------------------
     | Accessors
     |------------------------------------------------------------- */

    public function getAuthor(): string      { return $this->author; }
    public function getSubject(): string     { return $this->subject; }
    public function getDescription(): string { return $this->description; }
    public function getKeywords(): string    { return $this->keywords; }
    public function getLanguage(): string    { return $this->language; }

    public function getCreatedAt(): ?\DateTimeImmutable  { return $this->createdAt; }
    public function getModifiedAt(): ?\DateTimeImmutable { return $this->modifiedAt; }

    /* -------------------------------------------------------------
     | Fluent setters
     |------------------------------------------------------------- */

    public function setAuthor(string $author): static           { $this->author      = $author;      return $this; }
    public function setSubject(string $subject): static         { $this->subject     = $subject;     return $this; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }
    public function setKeywords(string $keywords): static       { $this->keywords    = $keywords;    return $this; }
    public function setLanguage(string $language): static       { $this->language    = $language;    return $this; }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = self::toDateTime($createdAt);

        return $this;
    }

    public function setModifiedAt(?\DateTimeInterface $modifiedAt): static
    {
        $this->modifiedAt = self::toDateTime($modifiedAt);

        return $this;
    }

    /* -------------------------------------------------------------
     | Conversion helpers
     |------------------------------------------------------------- */

    /** @return array<string, string|null> */
    public function toArray(): array
    {
        return [
            'author'      => $this->author,
            'subject'     => $this->subject,
            'description' => $this->description,
            'keywords'    => $this->keywords,
            'createdAt'   => $this->createdAt?->format(\DateTimeInterface::ATOM),
            'modifiedAt'  => $this->modifiedAt?->format(\DateTimeInterface::ATOM),
            'language'    => $this->language,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return array_filter(
            $this->toArray(),
            static fn (mixed $v) => $v !== '' && $v !== null,
        );
    }

    /* -------------------------------------------------------------
     | Internal
     |------------------------------------------------------------- */

    private static function toDateTime(\DateTimeInterface|string|null $value): ?\DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return \DateTimeImmutable::createFromInterface($value);
        }

        if (trim($value) === '') {
            return null;
        }

        return new \DateTimeImmutable($value);
    }
}
