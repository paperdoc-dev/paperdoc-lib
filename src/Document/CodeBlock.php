<?php

declare(strict_types=1);

namespace Paperdoc\Document;

use Paperdoc\Contracts\BlockElementInterface;

/**
 * Block of verbatim source code with an optional language hint. Maps
 * cleanly to:
 *
 *  - Markdown: ```` ```language … ``` ````
 *  - HTML: `<pre><code class="language-…">…</code></pre>`
 *  - DOCX: monospaced paragraph with preserved whitespace
 */
class CodeBlock implements BlockElementInterface, \JsonSerializable
{
    public function __construct(
        private string $code = '',
        private string $language = '',
    ) {}

    /* -------------------------------------------------------------
     | Static Factories
     |------------------------------------------------------------- */

    public static function make(string $code = '', string $language = ''): static
    {
        return new static($code, $language);
    }

    /* -------------------------------------------------------------
     | DocumentElementInterface
     |------------------------------------------------------------- */

    public function getType(): string { return 'code_block'; }

    /* -------------------------------------------------------------
     | Code
     |------------------------------------------------------------- */

    public function getCode(): string { return $this->code; }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Append a line to the existing code, separated by a single LF. No
     * newline is added if the code block is currently empty.
     */
    public function appendLine(string $line): static
    {
        $this->code = $this->code === '' ? $line : $this->code . "\n" . $line;

        return $this;
    }

    /** @return string[] */
    public function getLines(): array
    {
        if ($this->code === '') {
            return [];
        }

        return preg_split('/\r\n|\r|\n/', $this->code) ?: [];
    }

    /* -------------------------------------------------------------
     | Language hint
     |------------------------------------------------------------- */

    public function getLanguage(): string { return $this->language; }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function hasLanguage(): bool { return $this->language !== ''; }

    /* -------------------------------------------------------------
     | JsonSerializable
     |------------------------------------------------------------- */

    public function jsonSerialize(): mixed
    {
        $result = [
            'type' => 'code_block',
            'code' => $this->code,
        ];

        if ($this->language !== '') {
            $result['language'] = $this->language;
        }

        return $result;
    }
}
