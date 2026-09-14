<?php

declare(strict_types=1);

namespace MicroHis\Domain\Entities;

use InvalidArgumentException;

final class Bed
{
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_CLEANING = 'cleaning';

    private const VALID_STATUSES = [
        self::STATUS_AVAILABLE,
        self::STATUS_OCCUPIED,
        self::STATUS_CLEANING,
    ];

    public function __construct(
        private int $id,
        private string $code,
        private string $status
    ) {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de la cama debe ser mayor que cero.');
        }

        if (trim($code) === '') {
            throw new InvalidArgumentException('El código de la cama es obligatorio.');
        }

        if (!in_array($status, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException('El estado de la cama no es válido.');
        }
    }

    public function id(): int
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function occupy(): void
    {
        if (!$this->isAvailable()) {
            throw new InvalidArgumentException('La cama debe estar disponible antes de ser ocupada.');
        }

        $this->status = self::STATUS_OCCUPIED;
    }

    public function releaseToCleaning(): void
    {
        if ($this->status !== self::STATUS_OCCUPIED) {
            throw new InvalidArgumentException('Solo una cama ocupada puede pasar a limpieza.');
        }

        $this->status = self::STATUS_CLEANING;
    }

    public function markAvailable(): void
    {
        if ($this->status !== self::STATUS_CLEANING) {
            throw new InvalidArgumentException('La cama debe estar en limpieza antes de quedar disponible.');
        }

        $this->status = self::STATUS_AVAILABLE;
    }
}
