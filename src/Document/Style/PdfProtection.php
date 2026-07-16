<?php

declare(strict_types=1);

namespace Paperdoc\Document\Style;

use Paperdoc\Factory\DocumentHydrator;

final class PdfProtection implements \JsonSerializable
{
    public function __construct(
        private string $userPassword = '',
        private string $ownerPassword = '',
        private bool $allowPrint = true,
        private bool $allowCopy = true,
        private bool $allowModify = true,
        private bool $allowAnnotate = true,
    ) {}

    public static function make(string $userPassword = '', string $ownerPassword = ''): static
    {
        return new static($userPassword, $ownerPassword);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new static(
            DocumentHydrator::asString($data['userPassword'] ?? null),
            DocumentHydrator::asString($data['ownerPassword'] ?? null),
            DocumentHydrator::asBool($data['allowPrint'] ?? null, true),
            DocumentHydrator::asBool($data['allowCopy'] ?? null, true),
            DocumentHydrator::asBool($data['allowModify'] ?? null, true),
            DocumentHydrator::asBool($data['allowAnnotate'] ?? null, true),
        );
    }

    public function getUserPassword(): string { return $this->userPassword; }
    public function getOwnerPassword(): string { return $this->ownerPassword; }
    public function allowsPrint(): bool { return $this->allowPrint; }
    public function allowsCopy(): bool { return $this->allowCopy; }
    public function allowsModify(): bool { return $this->allowModify; }
    public function allowsAnnotate(): bool { return $this->allowAnnotate; }

    public function setUserPassword(string $password): static
    {
        $this->userPassword = $password;

        return $this;
    }

    public function setOwnerPassword(string $password): static
    {
        $this->ownerPassword = $password;

        return $this;
    }

    public function allowPrint(bool $allowed = true): static
    {
        $this->allowPrint = $allowed;

        return $this;
    }

    public function allowCopy(bool $allowed = true): static
    {
        $this->allowCopy = $allowed;

        return $this;
    }

    public function allowModify(bool $allowed = true): static
    {
        $this->allowModify = $allowed;

        return $this;
    }

    public function allowAnnotate(bool $allowed = true): static
    {
        $this->allowAnnotate = $allowed;

        return $this;
    }

    public function disallowPrint(): static
    {
        return $this->allowPrint(false);
    }

    public function disallowCopy(): static
    {
        return $this->allowCopy(false);
    }

    public function disallowModify(): static
    {
        return $this->allowModify(false);
    }

    public function disallowAnnotate(): static
    {
        return $this->allowAnnotate(false);
    }

    public function effectiveOwnerPassword(): string
    {
        return $this->ownerPassword !== '' ? $this->ownerPassword : $this->userPassword;
    }

    public function isEnabled(): bool
    {
        return $this->userPassword !== '' || $this->ownerPassword !== '';
    }

    public function permissionFlags(): int
    {
        // Signed 32-bit /P (PDF 1.3 revision 2): reserved bits clear → -64.
        $flags = -64;

        if ($this->allowPrint) {
            $flags |= 0x0004;
        }
        if ($this->allowModify) {
            $flags |= 0x0008;
        }
        if ($this->allowCopy) {
            $flags |= 0x0010;
        }
        if ($this->allowAnnotate) {
            $flags |= 0x0020;
        }

        return $flags;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'userPassword'  => $this->userPassword,
            'ownerPassword' => $this->ownerPassword,
            'allowPrint'    => $this->allowPrint,
            'allowCopy'     => $this->allowCopy,
            'allowModify'   => $this->allowModify,
            'allowAnnotate' => $this->allowAnnotate,
        ];
    }
}
