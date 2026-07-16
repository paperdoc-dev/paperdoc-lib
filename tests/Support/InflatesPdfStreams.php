<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Support;

/**
 * Replaces every Flate-compressed stream body of a PDF with its
 * inflated form, so tests can keep asserting on drawing operators.
 */
trait InflatesPdfStreams
{
    private function inflatePdf(string $pdf): string
    {
        // Image XObject streams end with "<data>endstream" (no newline),
        // page content streams with "<data>\nendstream" — both forms
        // must terminate the lazy quantifier, hence the optional \n.
        return preg_replace_callback(
            '/stream\r?\n(.*?)\r?\n?endstream/s',
            static function (array $m): string {
                $decoded = @gzuncompress($m[1]);

                return "stream\n" . ($decoded !== false ? $decoded : $m[1]) . "\nendstream";
            },
            $pdf,
        ) ?? $pdf;
    }
}
