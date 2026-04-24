<?php

declare(strict_types=1);

namespace Paperdoc\Document\Link;

use Paperdoc\Contracts\LinkInterface;

/**
 * Represents a hyperlink attached to a TextRun.
 *
 * Supports three flavours:
 *  - External URL     : setUrl('https://example.com')
 *  - Internal anchor  : setAnchor('section-1') → rendered as '#section-1'
 *  - Hybrid           : external URL with fragment anchor (url + anchor)
 */
class TextLink implements LinkInterface, \JsonSerializable
{
    private string $url = '';
    private string $anchor = '';
    private string $title = '';

    public function __construct(string $url = '', string $anchor = '', string $title = '')
    {
        $this->url = $url;
        $this->anchor = $anchor;
        $this->title = $title;
    }

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $url = '', string $anchor = '', string $title = ''): static
    {
        return new static($url, $anchor, $title);
    }

    /* -------------------------------------------------------------
     | Getters
     |------------------------------------------------------------- */

    public function getUrl(): string    { return $this->url; }
    public function getAnchor(): string { return $this->anchor; }
    public function getTitle(): string  { return $this->title; }

    /**
     * Fully-qualified href combining url and anchor fragment.
     * Returns e.g. "https://example.com#section-1" or just "#anchor".
     */
    public function getHref(): string
    {
        if ($this->url === '' && $this->anchor === '') {
            return '';
        }

        if ($this->url === '') {
            return '#' . ltrim($this->anchor, '#');
        }

        if ($this->anchor === '') {
            return $this->url;
        }

        return rtrim($this->url, '#') . '#' . ltrim($this->anchor, '#');
    }

    public function isExternal(): bool
    {
        return $this->url !== ''
            && preg_match('#^(https?|mailto|tel|ftp):#i', $this->url) === 1;
    }

    /* -------------------------------------------------------------
     | Fluent Setters
     |------------------------------------------------------------- */

    public function setUrl(string $v): static    { $this->url = $v; return $this; }
    public function setAnchor(string $v): static { $this->anchor = $v; return $this; }
    public function setTitle(string $v): static  { $this->title = $v; return $this; }

    /* -------------------------------------------------------------
     | Serialization
     |------------------------------------------------------------- */

    public function toArray(): array
    {
        $out = ['url' => $this->url];

        if ($this->anchor !== '') {
            $out['anchor'] = $this->anchor;
        }

        if ($this->title !== '') {
            $out['title'] = $this->title;
        }

        return $out;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
