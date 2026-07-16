<?php

declare(strict_types=1);

namespace Paperdoc\Document\Style;

/**
 * PDF document protection: user/owner passwords and usage permissions
 * (standard security handler, revision 3, RC4-128).
 */
final class Protection implements \JsonSerializable
{
    public function __construct(
        private string $userPassword = '',
        private string $ownerPassword = '',
        private bool $allowPrint = true,
        private bool $allowModify = false,
        private bool $allowCopy = false,
        private bool $allowAnnotate = false,
    ) {}

    public static function make(): static
    {
        return new static();
    }

    public function getUserPassword(): string { return $this->userPassword; }
    public function getOwnerPassword(): string { return $this->ownerPassword; }
    public function canPrint(): bool { return $this->allowPrint; }
    public function canModify(): bool { return $this->allowModify; }
    public function canCopy(): bool { return $this->allowCopy; }
    public function canAnnotate(): bool { return $this->allowAnnotate; }

    public function setUserPassword(string $v): static { $this->userPassword = $v; return $this; }
    public function setOwnerPassword(string $v): static { $this->ownerPassword = $v; return $this; }
    public function allowPrint(bool $v = true): static { $this->allowPrint = $v; return $this; }
    public function allowModify(bool $v = true): static { $this->allowModify = $v; return $this; }
    public function allowCopy(bool $v = true): static { $this->allowCopy = $v; return $this; }
    public function allowAnnotate(bool $v = true): static { $this->allowAnnotate = $v; return $this; }

    /**
     * The /P entry: a signed 32-bit flag word (revision 3 layout).
     */
    public function permissionFlags(): int
    {
        // Signed 32-bit /P (revision 3 layout): reserved bits clear → -3904 (0xFFFFF0C0).
        $p = -3904;

        if ($this->allowPrint) {
            $p |= 4 | 2048;
        }
        if ($this->allowModify) {
            $p |= 8 | 1024;
        }
        if ($this->allowCopy) {
            $p |= 16 | 512;
        }
        if ($this->allowAnnotate) {
            $p |= 32 | 256;
        }

        return $p;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'hasUserPassword'  => $this->userPassword !== '',
            'hasOwnerPassword' => $this->ownerPassword !== '',
            'allowPrint'       => $this->allowPrint,
            'allowModify'      => $this->allowModify,
            'allowCopy'        => $this->allowCopy,
            'allowAnnotate'    => $this->allowAnnotate,
        ];
    }
}
