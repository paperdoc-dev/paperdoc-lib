<?php

declare(strict_types=1);

namespace Paperdoc\Support\Pdf;

/**
 * PDF standard security handler, revision 3 (RC4, 128-bit keys).
 * Implements Algorithms 3.2–3.5 of the PDF 1.7 specification with a
 * pure-PHP RC4 (OpenSSL 3 dropped RC4 from its default provider).
 */
final class PdfEncryption
{
    private const PAD = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08"
        . "\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";

    private const KEY_BYTES = 16;

    private string $encryptionKey;
    private string $oValue;
    private string $uValue;

    public function __construct(
        string $userPassword,
        string $ownerPassword,
        private readonly int $permissions,
        private readonly string $fileId,
    ) {
        $this->oValue = $this->computeOwnerValue($userPassword, $ownerPassword);
        $this->encryptionKey = $this->computeEncryptionKey($userPassword);
        $this->uValue = $this->computeUserValue();
    }

    public function getFileIdHex(): string
    {
        return strtoupper(bin2hex($this->fileId));
    }

    public function buildEncryptDict(): string
    {
        return sprintf(
            '<< /Filter /Standard /V 2 /R 3 /Length 128 /P %d /O <%s> /U <%s> >>',
            $this->permissions,
            strtoupper(bin2hex($this->oValue)),
            strtoupper(bin2hex($this->uValue)),
        );
    }

    /**
     * Encrypts every string literal and stream payload of an object
     * body with the object-specific RC4 key. Encrypted strings are
     * re-emitted as hex strings to avoid escaping issues.
     */
    public function encryptObject(int $objectNumber, string $content): string
    {
        $key = $this->objectKey($objectNumber);

        $streamStart = strpos($content, "stream\n");

        if ($streamStart !== false) {
            $payloadStart = $streamStart + 7;
            $payloadEnd = strrpos($content, 'endstream');
            if ($payloadEnd === false || $payloadEnd < $payloadStart) {
                return $this->encryptStringLiterals($content, $key);
            }
            $payload = substr($content, $payloadStart, $payloadEnd - $payloadStart);
            $trailingNewline = str_ends_with($payload, "\n");
            if ($trailingNewline) {
                $payload = substr($payload, 0, -1);
            }

            $dict = $this->encryptStringLiterals(substr($content, 0, $streamStart), $key);

            return $dict . "stream\n" . $this->rc4($key, $payload) . ($trailingNewline ? "\n" : '')
                . substr($content, $payloadEnd);
        }

        return $this->encryptStringLiterals($content, $key);
    }

    private function encryptStringLiterals(string $content, string $key): string
    {
        return (string) preg_replace_callback(
            '/\((?:\\\\.|[^\\\\()])*\)/s',
            function (array $m) use ($key): string {
                $raw = $this->unescapePdfString(substr($m[0], 1, -1));

                return '<' . strtoupper(bin2hex($this->rc4($key, $raw))) . '>';
            },
            $content,
        );
    }

    private function unescapePdfString(string $escaped): string
    {
        return (string) preg_replace_callback(
            '/\\\\(.)/s',
            static fn (array $m): string => $m[1],
            $escaped,
        );
    }

    private function objectKey(int $objectNumber): string
    {
        $digest = md5(
            $this->encryptionKey
            . substr(pack('V', $objectNumber), 0, 3)
            . substr(pack('V', 0), 0, 2),
            true,
        );

        return substr($digest, 0, min(16, self::KEY_BYTES + 5));
    }

    private function pad(string $password): string
    {
        return substr($password . self::PAD, 0, 32);
    }

    private function computeOwnerValue(string $userPassword, string $ownerPassword): string
    {
        $key = md5($this->pad($ownerPassword !== '' ? $ownerPassword : $userPassword), true);
        for ($i = 0; $i < 50; $i++) {
            $key = md5($key, true);
        }
        $key = substr($key, 0, self::KEY_BYTES);

        $o = $this->rc4($key, $this->pad($userPassword));
        for ($i = 1; $i <= 19; $i++) {
            $o = $this->rc4($this->xorKey($key, $i), $o);
        }

        return $o;
    }

    private function computeEncryptionKey(string $userPassword): string
    {
        $digest = md5(
            $this->pad($userPassword) . $this->oValue . pack('V', $this->permissions) . $this->fileId,
            true,
        );

        for ($i = 0; $i < 50; $i++) {
            $digest = md5(substr($digest, 0, self::KEY_BYTES), true);
        }

        return substr($digest, 0, self::KEY_BYTES);
    }

    private function computeUserValue(): string
    {
        $u = $this->rc4($this->encryptionKey, md5(self::PAD . $this->fileId, true));
        for ($i = 1; $i <= 19; $i++) {
            $u = $this->rc4($this->xorKey($this->encryptionKey, $i), $u);
        }

        return $u . str_repeat("\x00", 16);
    }

    private function xorKey(string $key, int $iteration): string
    {
        $out = '';
        $len = strlen($key);
        for ($i = 0; $i < $len; $i++) {
            $out .= chr((ord($key[$i]) ^ $iteration) & 0xFF);
        }

        return $out;
    }

    private function rc4(string $key, string $data): string
    {
        $s = range(0, 255);
        $keyLen = strlen($key);
        $j = 0;

        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $s[$i] + ord($key[$i % $keyLen])) & 0xFF;
            [$s[$i], $s[$j]] = [$s[$j], $s[$i]];
        }

        $out = '';
        $i = $j = 0;
        $len = strlen($data);

        for ($k = 0; $k < $len; $k++) {
            $i = ($i + 1) & 0xFF;
            $j = ($j + $s[$i]) & 0xFF;
            [$s[$i], $s[$j]] = [$s[$j], $s[$i]];
            $out .= $data[$k] ^ chr($s[($s[$i] + $s[$j]) & 0xFF]);
        }

        return $out;
    }
}
