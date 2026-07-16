<?php

declare(strict_types=1);

namespace Paperdoc\Llm\Dto;

use Paperdoc\Support\Cast;

class PageContent
{
    public string $title = '';

    /** @var string[] */
    public array $paragraphs = [];

    /** @var array<int, string[][]> */
    public array $tables = [];

    public float $confidence = 0.0;

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        $page = new self();
        $page->title      = is_string($data['title'] ?? null) ? $data['title'] : '';
        $page->paragraphs = array_values(array_filter(
            Cast::asList($data['paragraphs'] ?? []),
            is_string(...),
        ));
        $page->tables     = self::normalizeTables($data['tables'] ?? null);
        $page->confidence = max(0.0, min(1.0, Cast::asFloat($data['confidence'] ?? 0.0)));

        return $page;
    }

    /**
     * @return array<int, string[][]>
     */
    private static function normalizeTables(mixed $tables): array
    {
        if (! is_array($tables)) {
            return [];
        }

        $normalized = [];
        foreach (array_values($tables) as $table) {
            if (! is_array($table)) {
                continue;
            }

            $rows = [];
            foreach (array_values($table) as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $cells = [];
                foreach (array_values($row) as $cell) {
                    $cells[] = Cast::asString($cell);
                }
                $rows[] = $cells;
            }
            $normalized[] = $rows;
        }

        return $normalized;
    }
}
