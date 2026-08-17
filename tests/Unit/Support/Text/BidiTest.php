<?php

declare(strict_types=1);

namespace Paperdoc\Tests\Unit\Support\Text;

use PHPUnit\Framework\TestCase;
use Paperdoc\Support\Text\ArabicShaper;
use Paperdoc\Support\Text\Bidi;

/**
 * Les sorties attendues ont été vérifiées contre fribidi, l'implémentation
 * de référence de [UAX #9].
 */
class BidiTest extends TestCase
{
    /**
     * Le texte sans caractère droite-à-gauche doit ressortir intact — et
     * sans payer le coût de l'algorithme.
     */
    public function test_left_to_right_text_is_untouched(): void
    {
        foreach ([
            'The total is 1234 euros',
            'Résumé du trimestre — 1234 €',
            'Общая сумма продаж составляет 1234 евро',
            '売上高の合計は1234ユーロです',
            '',
        ] as $text) {
            $this->assertSame($text, Bidi::reorder($text));
        }
    }

    public function test_hebrew_is_reordered_right_to_left(): void
    {
        $this->assertSame('םלוע םולש', Bidi::reorder('שלום עולם'));
    }

    /**
     * Un nombre inséré dans du texte droite-à-gauche garde son ordre
     * gauche-à-droite : « 1234 » ne devient jamais « 4321 ».
     */
    public function test_numbers_keep_their_own_direction(): void
    {
        $visual = Bidi::reorder('סך המכירות הוא 1234 אירו');

        $this->assertStringContainsString('1234', $visual);
        $this->assertSame('וריא 1234 אוה תוריכמה ךס', $visual);
    }

    public function test_embedded_latin_run_keeps_its_order(): void
    {
        $this->assertSame('םויה USD 50 אוה ריחמה', Bidi::reorder('המחיר הוא 50 USD היום'));
    }

    /**
     * Règle L4 : une parenthèse vue dans un niveau droite-à-gauche doit
     * être remplacée par son miroir, sinon elle pointe du mauvais côté.
     */
    public function test_brackets_are_mirrored(): void
    {
        $this->assertSame('ןאכ (תירבע) טסקט', Bidi::reorder('טקסט (עברית) כאן'));
    }

    /**
     * Le sens du paragraphe décide de la place des blocs les uns par
     * rapport aux autres. Les deux sorties correspondent à fribidi
     * lancé avec --rtl puis --ltr.
     */
    public function test_paragraph_direction_can_be_forced(): void
    {
        $this->assertSame('Total: 42 םולש', Bidi::reorder('שלום Total: 42', true));
        $this->assertSame('םולש Total: 42', Bidi::reorder('שלום Total: 42', false));
    }

    /**
     * Une ligne entièrement latine ne bouge pas même dans un paragraphe
     * droite-à-gauche : sans niveau impair, la règle L2 n'inverse rien.
     * fribidi --rtl se comporte de même.
     */
    public function test_pure_latin_line_is_stable_in_an_rtl_paragraph(): void
    {
        $this->assertSame('Total: 42', Bidi::reorder('Total: 42', true));
    }

    public function test_arabic_shaping_selects_contextual_forms(): void
    {
        // Formes attendues du bloc Arabic Presentation Forms-B.
        $expected = [
            'سلام'  => ['FEB3', 'FEFC', 'FEE1'], // seen initial, ligature lam-alef finale, meem isolé
            'بسم'   => ['FE91', 'FEB4', 'FEE2'], // initial, médian, final
            'لا'     => ['FEFB'],                 // ligature lam-alef isolée
            'الله'  => ['FE8D', 'FEDF', 'FEE0', 'FEEA'],
            'مرحبا' => ['FEE3', 'FEAE', 'FEA3', 'FE92', 'FE8E'],
        ];

        foreach ($expected as $source => $forms) {
            $shaped = ArabicShaper::shape($source);
            $actual = [];

            foreach (preg_split('//u', $shaped, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $char) {
                $actual[] = sprintf('%04X', mb_ord($char, 'UTF-8'));
            }

            $this->assertSame($forms, $actual, "façonnage de « {$source} »");
        }
    }

    public function test_shaping_leaves_other_scripts_alone(): void
    {
        foreach (['Le total 1234', 'Общая сумма', 'שלום עולם'] as $text) {
            $this->assertSame($text, ArabicShaper::shape($text));
        }
    }

    /**
     * Le façonnage doit précéder le réordonnancement : c'est l'ordre
     * logique qui détermine les voisines d'une lettre.
     */
    public function test_shaping_then_reordering_matches_reference(): void
    {
        $cases = [
            'إجمالي المبيعات هو 1234 يورو' => 'ﻭﺭﻮﻳ 1234 ﻮﻫ ﺕﺎﻌﻴﺒﻤﻟﺍ ﻲﻟﺎﻤﺟﺇ',
            'عدد الصفحات 15 من 20'          => '20 ﻦﻣ 15 ﺕﺎﺤﻔﺼﻟﺍ ﺩﺪﻋ',
            'مرحبا world 42'                => 'world 42 ﺎﺒﺣﺮﻣ',
        ];

        foreach ($cases as $logical => $visual) {
            $this->assertSame($visual, Bidi::reorder(ArabicShaper::shape($logical)));
        }
    }
}
