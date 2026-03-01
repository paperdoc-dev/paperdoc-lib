<?php

declare(strict_types=1);

namespace Paperdoc\Renderers;

use Paperdoc\Contracts\DocumentInterface;
use Paperdoc\Document\{Paragraph, Section, Table};
use Paperdoc\Support\Ole2\Ole2Writer;

/**
 * Renderer PPT natif (PowerPoint 97-2003 Binary File Format).
 *
 * Produit des fichiers .ppt valides conformes à [MS-PPT]
 * en utilisant des records binaires dans un flux OLE2
 * « PowerPoint Document ». Pas de dépendance tierce.
 */
class PptRenderer extends AbstractRenderer
{
    // Container record types
    private const RT_DOCUMENT              = 0x03E8;
    private const RT_SLIDE                 = 0x03EE;
    private const RT_SLIDE_BASE            = 0x03EC;
    private const RT_DRAWING               = 0x040C;
    private const RT_DRAWING_GROUP         = 0x040B;
    private const RT_LIST                  = 0x1772;
    private const RT_SLIDE_LIST_WITH_TEXT  = 0x0FF0;

    // Atom record types
    private const RT_DOCUMENT_ATOM         = 0x03E9;
    private const RT_SLIDE_ATOM            = 0x03EF;
    private const RT_TEXT_HEADER_ATOM      = 0x0F9F;
    private const RT_TEXT_CHARS_ATOM       = 0x0FA0;
    private const RT_TEXT_BYTES_ATOM       = 0x0FA8;
    private const RT_SLIDE_PERSIST_ATOM    = 0x03F3;
    private const RT_END_DOCUMENT_ATOM     = 0x03EA;
    private const RT_USER_EDIT_ATOM        = 0x0FF5;
    private const RT_PERSIST_DIR           = 0x1772;
    private const RT_CURRENT_USER_ATOM     = 0x0FF6;
    private const RT_ENVIRONMENT           = 0x1010;

    // Text type constants
    private const TEXTTYPE_TITLE = 0;
    private const TEXTTYPE_BODY  = 1;

    public function getFormat(): string { return 'ppt'; }

    public function render(DocumentInterface $document): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'paperdoc_ppt_');

        try {
            $this->buildPpt($document, $tmp);

            return file_get_contents($tmp) ?: '';
        } finally {
            @unlink($tmp);
        }
    }

    public function save(DocumentInterface $document, string $filename): void
    {
        $this->ensureDirectoryWritable($filename);
        $this->buildPpt($document, $filename);
    }

    /* =============================================================
     | Builder
     |============================================================= */

    private function buildPpt(DocumentInterface $document, string $filename): void
    {
        $sections = $document->getSections();

        if (empty($sections)) {
            $sections = [new Section('slide-1')];
        }

        $pptStream    = $this->buildPowerPointDocument($sections);
        $currentUser  = $this->buildCurrentUserStream();

        $writer = new Ole2Writer();
        $writer->addStream('PowerPoint Document', $pptStream);
        $writer->addStream('Current User', $currentUser);

        file_put_contents($filename, $writer->build());
    }

    /* =============================================================
     | PowerPoint Document Stream
     |============================================================= */

    private function buildPowerPointDocument(array $sections): string
    {
        $slideCount = count($sections);

        // DocumentContainer
        $docContent = '';

        // DocumentAtom
        $docContent .= $this->atom(self::RT_DOCUMENT_ATOM,
            pack('V', 0)                // slideSize (on-screen)
            . pack('V', 0)              // notesSize
            . pack('V', 0)              // serverZoom num
            . pack('V', 0)              // serverZoom den
            . pack('V', 0)              // notesMasterPersistId
            . pack('V', 0)              // handoutMasterPersistId
            . pack('v', 1)              // firstSlideNum
            . pack('v', 0x0000)         // slideSizeType (on-screen 4:3)
            . pack('C', 0)              // fSaveWithFonts
            . pack('C', 0)              // fOmitTitlePlace
            . pack('C', 0)              // fRightToLeft
            . pack('C', 0)              // fShowComments
        );

        // Environment container (minimal)
        $envContent = $this->atom(0x03F2, str_repeat("\x00", 8)); // TextMasterStyleAtom placeholder
        $docContent .= $this->container(self::RT_ENVIRONMENT, $envContent);

        // SlideListWithText container
        $slwtContent = '';

        foreach ($sections as $i => $section) {
            // SlidePersistAtom
            $slwtContent .= $this->atom(self::RT_SLIDE_PERSIST_ATOM,
                pack('V', $i + 1)       // persistIdRef (1-based slide ID)
                . pack('V', 0x04)       // flags
                . pack('V', $i)         // numberTexts
                . pack('V', $i + 1)     // slideId
                . pack('V', 0)          // reserved
            );

            // Text content for this slide
            $texts = $this->collectSlideTexts($section);

            foreach ($texts as $j => $text) {
                // TextHeaderAtom — title for first, body for rest
                $textType = ($j === 0) ? self::TEXTTYPE_TITLE : self::TEXTTYPE_BODY;
                $slwtContent .= $this->atom(self::RT_TEXT_HEADER_ATOM, pack('V', $textType));

                // Text data
                if (mb_check_encoding($text, 'ASCII')) {
                    $slwtContent .= $this->atom(self::RT_TEXT_BYTES_ATOM, $text);
                } else {
                    $utf16 = mb_convert_encoding($text, 'UTF-16LE', 'UTF-8');
                    $slwtContent .= $this->atom(self::RT_TEXT_CHARS_ATOM, $utf16);
                }
            }
        }

        $docContent .= $this->container(self::RT_SLIDE_LIST_WITH_TEXT, $slwtContent);

        // End document atom
        $docContent .= $this->atom(self::RT_END_DOCUMENT_ATOM, '');

        $pptStream = $this->container(self::RT_DOCUMENT, $docContent);

        // Persist directory (maps persist IDs to stream offsets)
        $persistData  = pack('V', ($slideCount & 0xFFFFF) | (1 << 20)); // count in high bits, startId=1 in low
        for ($i = 0; $i < $slideCount; $i++) {
            $persistData .= pack('V', 0); // offset placeholder
        }
        $pptStream .= $this->atom(self::RT_PERSIST_DIR, $persistData);

        // UserEditAtom
        $pptStream .= $this->atom(self::RT_USER_EDIT_ATOM,
            pack('V', 0)                // lastSlideIdRef
            . pack('v', 0x0003)         // version
            . pack('C', 0x00)           // minorVersion
            . pack('C', 0x03)           // majorVersion
            . pack('V', 0)              // offsetLastEdit
            . pack('V', strlen($pptStream) - 20) // offsetPersistDirectory (before this atom)
            . pack('V', 1)              // docPersistIdRef
            . pack('V', $slideCount)    // persistIdSeed
            . pack('v', 0)              // lastView
            . pack('v', 0)              // unused
        );

        return $pptStream;
    }

    /* =============================================================
     | Current User Stream
     |============================================================= */

    private function buildCurrentUserStream(): string
    {
        $userName = 'Paperdoc';
        $userNameAnsi = $userName;

        $data  = pack('V', 20 + strlen($userNameAnsi)); // size
        $data .= pack('V', 0xF3D1C4DF);  // headerToken (non-encrypted)
        $data .= pack('V', 0);            // offsetToCurrentEdit
        $data .= pack('v', strlen($userNameAnsi)); // lenUserName
        $data .= pack('v', 0x0003);       // docFileVersion
        $data .= pack('C', 0x03);         // majorVersion
        $data .= pack('C', 0x00);         // minorVersion
        $data .= pack('v', 0);            // unused
        $data .= $userNameAnsi;

        return $this->atom(self::RT_CURRENT_USER_ATOM, $data);
    }

    /* =============================================================
     | Text Collection
     |============================================================= */

    /**
     * @return string[]
     */
    private function collectSlideTexts(Section $section): array
    {
        $texts = [];

        foreach ($section->getElements() as $element) {
            if ($element instanceof Paragraph) {
                $text = trim($element->getPlainText());
                if ($text !== '') {
                    $texts[] = $text;
                }
            } elseif ($element instanceof Table) {
                foreach ($element->getRows() as $row) {
                    $cells = [];
                    foreach ($row->getCells() as $cell) {
                        $cells[] = $cell->getPlainText();
                    }
                    $line = implode(' | ', $cells);
                    if (trim($line) !== '') {
                        $texts[] = $line;
                    }
                }
            }
        }

        if (empty($texts)) {
            $texts[] = $section->getName() ?: '';
        }

        return $texts;
    }

    /* =============================================================
     | Record Helpers
     |============================================================= */

    /**
     * Build an atom record (non-container).
     * recVer = 0x0, recInstance = 0x000
     */
    private function atom(int $type, string $data, int $instance = 0): string
    {
        $verInstance = ($instance << 4) & 0xFFF0; // version = 0
        return pack('v', $verInstance) . pack('v', $type) . pack('V', strlen($data)) . $data;
    }

    /**
     * Build a container record.
     * recVer = 0xF
     */
    private function container(int $type, string $data, int $instance = 0): string
    {
        $verInstance = (($instance << 4) | 0x0F) & 0xFFFF; // version = 0xF (container)
        return pack('v', $verInstance) . pack('v', $type) . pack('V', strlen($data)) . $data;
    }
}
