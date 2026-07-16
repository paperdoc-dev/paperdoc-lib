<?php

declare(strict_types=1);

namespace Paperdoc\Support\Pdf;

use Paperdoc\Document\Style\PdfProtection;

final class PdfStandardSecurity
{
    private const PADDING = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08"
        . "\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";

    public function __construct(
        private readonly PdfProtection $protection,
        private readonly string $fileId,
    ) {}

    public function buildEncryptDictionary(int $objectNumber): PdfObject
    {
        return new PdfObject(
            $objectNumber,
            sprintf(
                '<< /Filter /Standard /V 1 /R 2 /O <%s> /U <%s> /P %d >>',
                bin2hex($this->ownerKey()),
                bin2hex($this->userKey()),
                $this->protection->permissionFlags(),
            )
        );
    }

    public function encryptString(string $bytes, int $objectNumber, int $generation = 0): string
    {
        return $this->rc4($this->objectKey($objectNumber, $generation), $bytes);
    }

    private function userKey(): string
    {
        return $this->rc4($this->documentKey(), self::PADDING);
    }

    private function ownerKey(): string
    {
        $ownerKey = substr(md5($this->padPassword($this->protection->effectiveOwnerPassword()), true), 0, 5);

        return $this->rc4($ownerKey, $this->padPassword($this->protection->getUserPassword()));
    }

    private function documentKey(): string
    {
        $material = $this->padPassword($this->protection->getUserPassword())
            . $this->ownerKey()
            . pack('V', $this->protection->permissionFlags())
            . $this->fileId;

        return substr(md5($material, true), 0, 5);
    }

    private function objectKey(int $objectNumber, int $generation): string
    {
        $suffix = substr(pack('V', $objectNumber), 0, 3) . substr(pack('V', $generation), 0, 2);

        return substr(md5($this->documentKey() . $suffix, true), 0, 10);
    }

    private function padPassword(string $password): string
    {
        $bytes = substr($password, 0, 32);

        return str_pad($bytes, 32, self::PADDING, STR_PAD_RIGHT);
    }

    private function rc4(string $key, string $data): string
    {
        $keyLength = strlen($key);
        $state = range(0, 255);
        $j = 0;

        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $state[$i] + ord($key[$i % $keyLength])) % 256;
            [$state[$i], $state[$j]] = [$state[$j], $state[$i]];
        }

        $i = 0;
        $j = 0;
        $output = '';
        $length = strlen($data);

        for ($index = 0; $index < $length; $index++) {
            $i = ($i + 1) % 256;
            $j = ($j + $state[$i]) % 256;
            [$state[$i], $state[$j]] = [$state[$j], $state[$i]];
            $k = $state[($state[$i] + $state[$j]) % 256];
            $output .= chr((ord($data[$index]) ^ $k) & 0xFF);
        }

        return $output;
    }
}
