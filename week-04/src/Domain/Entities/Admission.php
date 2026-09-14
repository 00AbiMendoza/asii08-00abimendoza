<?php

declare(strict_types=1);

namespace MicroHis\Domain\Entities;

use InvalidArgumentException;

final class Admission
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';

    public function __construct(
        private int $id,
        private string $patientCode,
        private int $bedId,
        private string $status
    ) {
        if ($id <= 0) {
            throw new InvalidArgumentException('El identificador de la admisión debe ser mayor que cero.');
        }

        if (trim($patientCode) === '') {
            throw new InvalidArgumentException('El código ficticio del paciente es obligatorio.');
        }

        if ($bedId <= 0) {
            throw new InvalidArgumentException('La cama asignada debe ser válida.');
        }

        if (!in_array($status, [self::STATUS_ACTIVE, self::STATUS_CLOSED], true)) {
            throw new InvalidArgumentException('El estado de la admisión no es válido.');
        }
    }

    public function id(): int
    {
        return $this->id;
    }

    public function patientCode(): string
    {
        return $this->patientCode;
    }

    public function bedId(): int
    {
        return $this->bedId;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function transferToBed(int $destinationBedId): void
    {
        if (!$this->isActive()) {
            throw new InvalidArgumentException('Solo una admisión activa puede trasladarse.');
        }

        if ($destinationBedId <= 0) {
            throw new InvalidArgumentException('La cama destino debe ser válida.');
        }

        if ($destinationBedId === $this->bedId) {
            throw new InvalidArgumentException('La cama destino debe ser distinta de la cama actual.');
        }

        $this->bedId = $destinationBedId;
    }

    public function discharge(): void
    {
        if (!$this->isActive()) {
            throw new InvalidArgumentException('Solo una admisión activa puede darse de alta.');
        }

        $this->status = self::STATUS_CLOSED;
    }
}
