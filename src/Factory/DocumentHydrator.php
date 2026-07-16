<?php

declare(strict_types=1);

namespace Paperdoc\Factory;

use Paperdoc\Contracts\BlockElementInterface;
use Paperdoc\Contracts\DocumentElementInterface;
use Paperdoc\Document\Blockquote;
use Paperdoc\Document\Bookmark;
use Paperdoc\Document\CodeBlock;
use Paperdoc\Document\Footnote;
use Paperdoc\Document\Heading;
use Paperdoc\Document\HorizontalRule;
use Paperdoc\Document\Image;
use Paperdoc\Document\Link\TextLink;
use Paperdoc\Document\ListBlock;
use Paperdoc\Document\ListItem;
use Paperdoc\Document\PageBreak;
use Paperdoc\Document\Paragraph;
use Paperdoc\Document\Style\PageSetup;
use Paperdoc\Document\Style\ParagraphStyle;
use Paperdoc\Document\Style\RunningElement;
use Paperdoc\Document\Style\TableStyle;
use Paperdoc\Document\Style\TextStyle;
use Paperdoc\Document\Table;
use Paperdoc\Document\TableCell;
use Paperdoc\Document\TableRow;
use Paperdoc\Document\TextRun;
use Paperdoc\Document\TextZone;
use Paperdoc\Enum\Alignment;
use Paperdoc\Enum\BorderStyle;
use Paperdoc\Enum\PageSize;
use Paperdoc\Exceptions\InvalidDocumentException;
use Paperdoc\Support\Cast;

final class DocumentHydrator
{
    /**
     * @param array<string, mixed> $data
     */
    public static function elementFromArray(array $data): DocumentElementInterface
    {
        return match ($data['type'] ?? null) {
            'heading' => self::headingFromArray($data),
            'paragraph' => self::paragraphFromArray($data),
            'list' => self::listFromArray($data),
            'blockquote' => self::blockquoteFromArray($data),
            'code_block' => self::codeBlockFromArray($data),
            'bookmark' => self::bookmarkFromArray($data),
            'table' => self::tableFromArray($data),
            'image' => self::imageFromArray($data),
            'page_break' => new PageBreak(),
            'horizontal_rule' => self::horizontalRuleFromArray($data),
            'text_zone' => self::textZoneFromArray($data),
            default => throw new InvalidDocumentException(sprintf(
                'Unsupported document element type "%s".',
                self::asString($data['type'] ?? null)
            )),
        };
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function headingFromArray(array $data): Heading
    {
        $heading = new Heading(
            self::asInt($data['level'] ?? null, 1),
            self::asString($data['id'] ?? null),
        );

        foreach (self::runsFromArray($data['runs'] ?? null, self::asString($data['text'] ?? null)) as $run) {
            $heading->addRun($run);
        }

        return $heading;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function paragraphFromArray(array $data): Paragraph
    {
        $paragraph = new Paragraph(self::paragraphStyleFromArrayOrNull($data['style'] ?? null));

        foreach (self::runsFromArray($data['runs'] ?? null, self::asString($data['text'] ?? null)) as $run) {
            $paragraph->addRun($run);
        }

        return $paragraph;
    }

    /**
     * @return TextRun[]
     */
    public static function runsFromArray(mixed $items, string $fallbackText = ''): array
    {
        $list = self::asList($items);
        if ($list === []) {
            return $fallbackText === '' ? [] : [new TextRun($fallbackText)];
        }

        $runs = [];

        foreach ($list as $item) {
            if (! is_array($item)) {
                continue;
            }

            $map = self::asMap($item);

            $runs[] = new TextRun(
                self::asString($map['text'] ?? null),
                self::textStyleFromArrayOrNull($map['style'] ?? null),
                self::textLinkFromArrayOrNull($map['link'] ?? null),
                is_array($map['footnote'] ?? null) ? Footnote::fromArray(self::asMap($map['footnote'])) : null,
            );
        }

        return $runs !== [] || $fallbackText === '' ? $runs : [new TextRun($fallbackText)];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function listFromArray(array $data): ListBlock
    {
        $list = new ListBlock(
            self::asString($data['style'] ?? null, ListBlock::STYLE_BULLET),
            self::asInt($data['start'] ?? null, 1),
        );

        foreach (self::asList($data['items'] ?? null) as $itemData) {
            if (! is_array($itemData)) {
                continue;
            }

            $list->addItem(self::listItemFromArray(self::asMap($itemData)));
        }

        return $list;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function listItemFromArray(array $data): ListItem
    {
        $item = new ListItem();

        foreach (self::runsFromArray($data['runs'] ?? null, self::asString($data['text'] ?? null)) as $run) {
            $item->addRun($run);
        }

        foreach (self::asList($data['blocks'] ?? null) as $blockData) {
            if (! is_array($blockData)) {
                continue;
            }

            $block = self::elementFromArray(self::asMap($blockData));

            if (! $block instanceof BlockElementInterface) {
                throw new InvalidDocumentException('List item child must be a block element.');
            }

            $item->addBlock($block);
        }

        return $item;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function blockquoteFromArray(array $data): Blockquote
    {
        $quote = new Blockquote();

        foreach (self::asList($data['elements'] ?? null) as $elementData) {
            if (! is_array($elementData)) {
                continue;
            }

            $quote->addElement(self::elementFromArray(self::asMap($elementData)));
        }

        return $quote;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function codeBlockFromArray(array $data): CodeBlock
    {
        return new CodeBlock(
            self::asString($data['code'] ?? null),
            self::asString($data['language'] ?? null),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function bookmarkFromArray(array $data): Bookmark
    {
        return new Bookmark(self::asString($data['id'] ?? null));
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function tableFromArray(array $data): Table
    {
        $table = new Table(self::tableStyleFromArrayOrNull($data['style'] ?? null));
        $columnWidths = $data['columnWidths'] ?? null;

        if (is_array($columnWidths)) {
            $table->setColumnWidths(array_map(
                static fn (mixed $value): float => self::asFloat($value),
                array_values($columnWidths),
            ));
        }

        foreach (self::asList($data['rows'] ?? null) as $rowData) {
            if (! is_array($rowData)) {
                continue;
            }

            $table->addRow(self::tableRowFromArray(self::asMap($rowData)));
        }

        return $table;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function tableRowFromArray(array $data): TableRow
    {
        $row = new TableRow();
        $row->setHeader(self::asBool($data['isHeader'] ?? null));

        foreach (self::asList($data['cells'] ?? null) as $cellData) {
            if (! is_array($cellData)) {
                continue;
            }

            $row->addCell(self::tableCellFromArray(self::asMap($cellData)));
        }

        return $row;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function tableCellFromArray(array $data): TableCell
    {
        $cell = new TableCell();
        $cell->setColspan(self::asInt($data['colspan'] ?? null, 1));
        $cell->setRowspan(self::asInt($data['rowspan'] ?? null, 1));

        foreach (self::asList($data['elements'] ?? null) as $elementData) {
            if (! is_array($elementData)) {
                continue;
            }

            $cell->addElement(self::elementFromArray(self::asMap($elementData)));
        }

        return $cell;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function imageFromArray(array $data): Image
    {
        $image = new Image(
            self::asString($data['src'] ?? null),
            self::asInt($data['width'] ?? null),
            self::asInt($data['height'] ?? null),
            self::asString($data['alt'] ?? null),
        );

        $dataUri = $data['dataUri'] ?? null;
        $mimeType = $data['mimeType'] ?? null;

        if (is_string($dataUri) && is_string($mimeType) && str_starts_with($dataUri, 'data:')) {
            $parts = explode(',', $dataUri, 2);
            if (isset($parts[1])) {
                $decoded = base64_decode($parts[1], true);
                if ($decoded !== false) {
                    $image->setData($decoded, $mimeType);
                }
            }
        }

        return $image;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function horizontalRuleFromArray(array $data): HorizontalRule
    {
        $width = $data['width'] ?? '100%';

        return HorizontalRule::make()
            ->setWidth(is_string($width) || is_int($width) || is_float($width) ? $width : '100%')
            ->setThickness(self::asFloat($data['thickness'] ?? null, 0.5))
            ->setColor(self::asString($data['color'] ?? null, '#999999'))
            ->setAlignment(Alignment::from(self::asString($data['alignment'] ?? null, Alignment::CENTER->value)))
            ->setMarginTop(self::asFloat($data['marginTop'] ?? null, 6.0))
            ->setMarginBottom(self::asFloat($data['marginBottom'] ?? null, 6.0));
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function textZoneFromArray(array $data): TextZone
    {
        $zone = new TextZone(
            self::asFloat($data['x'] ?? null),
            self::asFloat($data['y'] ?? null),
            self::asFloat($data['width'] ?? null, 200.0),
            self::asFloat($data['height'] ?? null, 100.0),
        );

        $zone->setPadding(self::asFloat($data['padding'] ?? null));
        $zone->setBackgroundColor(isset($data['backgroundColor']) ? self::asString($data['backgroundColor']) : null);

        if (isset($data['borderColor']) && self::asString($data['borderColor']) !== '') {
            $zone->setBorder(self::asString($data['borderColor']), self::asFloat($data['borderWidth'] ?? null, 0.5));
        }

        $zone->setOverflow(self::asString($data['overflow'] ?? null, TextZone::OVERFLOW_CLIP));

        foreach (self::asList($data['paragraphs'] ?? null) as $paragraphData) {
            if (! is_array($paragraphData)) {
                continue;
            }

            $zone->addParagraph(self::paragraphFromArray(self::asMap($paragraphData)));
        }

        return $zone;
    }

    public static function textStyleFromArrayOrNull(mixed $data): ?TextStyle
    {
        if (! is_array($data)) {
            return null;
        }

        $map = self::asMap($data);

        return TextStyle::make()
            ->setFontFamily(self::asString($map['fontFamily'] ?? null, 'Helvetica'))
            ->setFontSize(self::asFloat($map['fontSize'] ?? null, 12.0))
            ->setColor(self::asString($map['color'] ?? null, '#000000'))
            ->setBold(self::asBool($map['bold'] ?? null))
            ->setItalic(self::asBool($map['italic'] ?? null))
            ->setUnderline(self::asBool($map['underline'] ?? null))
            ->setStrikethrough(self::asBool($map['strikethrough'] ?? null))
            ->setHighlight(isset($map['highlight']) ? self::asString($map['highlight']) : null)
            ->setLetterSpacing(self::asFloat($map['letterSpacing'] ?? null));
    }

    public static function paragraphStyleFromArrayOrNull(mixed $data): ?ParagraphStyle
    {
        if (! is_array($data)) {
            return null;
        }

        $map = self::asMap($data);

        return ParagraphStyle::make()
            ->setAlignment(Alignment::from(self::asString($map['alignment'] ?? null, Alignment::LEFT->value)))
            ->setSpaceBefore(self::asFloat($map['spaceBefore'] ?? null))
            ->setSpaceAfter(self::asFloat($map['spaceAfter'] ?? null, 6.0))
            ->setLineSpacing(self::asFloat($map['lineSpacing'] ?? null, 1.15))
            ->setHeadingLevel(isset($map['headingLevel']) ? self::asInt($map['headingLevel']) : null)
            ->setFirstLineIndent(self::asFloat($map['firstLineIndent'] ?? null));
    }

    public static function tableStyleFromArrayOrNull(mixed $data): ?TableStyle
    {
        if (! is_array($data)) {
            return null;
        }

        $map = self::asMap($data);

        return TableStyle::make()
            ->setAlignment(Alignment::from(self::asString($map['alignment'] ?? null, Alignment::LEFT->value)))
            ->setBorderStyle(BorderStyle::from(self::asString($map['borderStyle'] ?? null, BorderStyle::SOLID->value)))
            ->setBorderWidth(self::asFloat($map['borderWidth'] ?? null, 0.5))
            ->setBorderColor(self::asString($map['borderColor'] ?? null, '#000000'))
            ->setCellPadding(self::asFloat($map['cellPadding'] ?? null, 4.0))
            ->setHeaderBg(array_key_exists('headerBg', $map) ? ($map['headerBg'] !== null ? self::asString($map['headerBg']) : null) : '#f3f4f6')
            ->setStripedBg(array_key_exists('stripedBg', $map) ? ($map['stripedBg'] !== null ? self::asString($map['stripedBg']) : null) : null);
    }

    public static function textLinkFromArrayOrNull(mixed $data): ?TextLink
    {
        if (! is_array($data)) {
            return null;
        }

        $map = self::asMap($data);

        return new TextLink(
            self::asString($map['url'] ?? null),
            self::asString($map['anchor'] ?? null),
            self::asString($map['title'] ?? null),
        );
    }

    public static function runningElementFromArrayOrNull(mixed $data): ?RunningElement
    {
        if (! is_array($data)) {
            return null;
        }

        $map = self::asMap($data);

        return RunningElement::make(self::asString($map['template'] ?? null))
            ->setAlignment(Alignment::from(self::asString($map['alignment'] ?? null, Alignment::CENTER->value)))
            ->setStyle(self::textStyleFromArrayOrNull($map['style'] ?? null) ?? TextStyle::make()->setFontSize(9.0)->setColor('#6B7280'))
            ->setHeight(self::asFloat($map['height'] ?? null, 30.0));
    }

    public static function pageSetupFromArrayOrNull(mixed $data): ?PageSetup
    {
        if (! is_array($data)) {
            return null;
        }

        $map = self::asMap($data);

        $setup = new PageSetup(
            self::asFloat($map['width'] ?? null, 595.28),
            self::asFloat($map['height'] ?? null, 841.89),
        );

        $size = $map['size'] ?? null;
        $orientation = self::asString($map['orientation'] ?? null, PageSetup::ORIENTATION_PORTRAIT);

        if (is_string($size) && $size !== '') {
            $setup->setSize(PageSize::from($size), $orientation);
        } else {
            $setup->setDimensions(
                self::asFloat($map['width'] ?? null, 595.28),
                self::asFloat($map['height'] ?? null, 841.89),
            );
        }

        $padding = $map['padding'] ?? null;
        if (is_array($padding)) {
            $paddingMap = self::asMap($padding);
            $setup->setPadding(
                self::asFloat($paddingMap['top'] ?? null, 40.0),
                self::asFloat($paddingMap['right'] ?? null, 40.0),
                self::asFloat($paddingMap['bottom'] ?? null, 40.0),
                self::asFloat($paddingMap['left'] ?? null, 40.0),
            );
        }

        $setup->setBackgroundColor(array_key_exists('backgroundColor', $map) ? ($map['backgroundColor'] !== null ? self::asString($map['backgroundColor']) : null) : null);
        $setup->setBackgroundImage(is_array($map['backgroundImage'] ?? null) ? self::imageFromArray(self::asMap($map['backgroundImage'])) : null);
        $setup->setBackgroundSize(self::asString($map['backgroundSize'] ?? null, PageSetup::BG_SIZE_COVER));
        $setup->setBackgroundPosition(self::asString($map['backgroundPosition'] ?? null, 'center center'));
        $setup->setBackgroundRepeat(self::asString($map['backgroundRepeat'] ?? null, 'no-repeat'));
        $setup->setColumnCount(self::asInt($map['columnCount'] ?? null, 1));
        $setup->setColumnGap(self::asFloat($map['columnGap'] ?? null, 18.0));

        return $setup;
    }

    /**
     * @return list<mixed>
     */
    public static function asList(mixed $value): array
    {
        return Cast::asList($value);
    }

    /**
     * @return array<string, mixed>
     */
    public static function asMap(mixed $value): array
    {
        return Cast::asMap($value);
    }

    public static function asString(mixed $value, string $default = ''): string
    {
        return Cast::asString($value, $default);
    }

    public static function asInt(mixed $value, int $default = 0): int
    {
        return Cast::asInt($value, $default);
    }

    public static function asFloat(mixed $value, float $default = 0.0): float
    {
        return Cast::asFloat($value, $default);
    }

    public static function asBool(mixed $value, bool $default = false): bool
    {
        return Cast::asBool($value, $default);
    }
}
