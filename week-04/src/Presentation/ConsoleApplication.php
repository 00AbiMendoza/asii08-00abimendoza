<?php

declare(strict_types=1);

namespace MicroHis\Presentation;

use MicroHis\Application\UseCases\DischargePatient;
use MicroHis\Application\UseCases\TransferPatient;
use Throwable;

final class ConsoleApplication
{
    public function __construct(
        private TransferPatient $transferPatient,
        private DischargePatient $dischargePatient
    ) {
    }

    public function run(array $arguments): int
    {
        $command = $arguments[1] ?? 'help';

        try {
            return match ($command) {
                'transfer' => $this->transfer($arguments),
                'discharge' => $this->discharge($arguments),
                'help' => $this->showHelp(),
                default => $this->unknownCommand($command),
            };
        } catch (Throwable $exception) {
            fwrite(STDERR, 'ERROR: ' . $exception->getMessage() . PHP_EOL);
            return 1;
        }
    }

    private function transfer(array $arguments): int
    {
        $admissionId = $this->positiveInteger($arguments[2] ?? null, 'admissionId');
        $destinationBedId = $this->positiveInteger($arguments[3] ?? null, 'destinationBedId');

        $this->transferPatient->execute($admissionId, $destinationBedId);

        echo "Traslado realizado correctamente." . PHP_EOL;
        return 0;
    }

    private function discharge(array $arguments): int
    {
        $admissionId = $this->positiveInteger($arguments[2] ?? null, 'admissionId');

        $this->dischargePatient->execute($admissionId);

        echo "Alta realizada correctamente." . PHP_EOL;
        return 0;
    }

    private function showHelp(): int
    {
        echo "Micro-HIS Traslados, altas y liberacion de camas" . PHP_EOL;
        echo "Uso:" . PHP_EOL;
        echo "  php micro-his.php transfer <admissionId> <destinationBedId>" . PHP_EOL;
        echo "  php micro-his.php discharge <admissionId>" . PHP_EOL;

        return 0;
    }

    private function unknownCommand(string $command): int
    {
        fwrite(STDERR, "Comando desconocido: {$command}" . PHP_EOL);
        return 1;
    }

    private function positiveInteger(mixed $value, string $name): int
    {
        if (
            !is_string($value) ||
            filter_var($value, FILTER_VALIDATE_INT) === false ||
            (int) $value <= 0
        ) {
            throw new \InvalidArgumentException(
                "El parametro {$name} debe ser un entero positivo."
            );
        }

        return (int) $value;
    }
}
