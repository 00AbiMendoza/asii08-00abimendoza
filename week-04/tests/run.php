<?php

declare(strict_types=1);

use MicroHis\Application\Contracts\TransactionManager;
use MicroHis\Application\UseCases\DischargePatient;
use MicroHis\Application\UseCases\TransferPatient;
use MicroHis\Domain\Entities\Admission;
use MicroHis\Domain\Entities\Bed;
use MicroHis\Domain\Exceptions\DomainException;
use MicroHis\Domain\Repositories\AdmissionRepository;
use MicroHis\Domain\Repositories\BedRepository;

require dirname(__DIR__) . '/autoload.php';

final class FakeBedRepository implements BedRepository
{
    /** @var array<int, Bed> */
    private array $beds = [];

    public bool $failOnSave = false;

    /** @param Bed[] $beds */
    public function __construct(array $beds)
    {
        foreach ($beds as $bed) {
            $this->beds[$bed->id()] = $bed;
        }
    }

    public function findById(int $id): ?Bed
    {
        return $this->beds[$id] ?? null;
    }

    public function save(Bed $bed): void
    {
        if ($this->failOnSave) {
            throw new RuntimeException('Error de persistencia simulado.');
        }

        $this->beds[$bed->id()] = $bed;
    }
}

final class FakeAdmissionRepository implements AdmissionRepository
{
    /** @var array<int, Admission> */
    private array $admissions = [];

    /** @param Admission[] $admissions */
    public function __construct(array $admissions)
    {
        foreach ($admissions as $admission) {
            $this->admissions[$admission->id()] = $admission;
        }
    }

    public function findById(int $id): ?Admission
    {
        return $this->admissions[$id] ?? null;
    }

    public function save(Admission $admission): void
    {
        $this->admissions[$admission->id()] = $admission;
    }
}

final class FakeTransactionManager implements TransactionManager
{
    public bool $began = false;
    public bool $committed = false;
    public bool $rolledBack = false;

    public function begin(): void
    {
        $this->began = true;
    }

    public function commit(): void
    {
        $this->committed = true;
    }

    public function rollback(): void
    {
        $this->rolledBack = true;
    }
}

function assertSameValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            $message .
            " | Esperado: " . var_export($expected, true) .
            " | Obtenido: " . var_export($actual, true)
        );
    }
}

$tests = [];

$tests['traslado exitoso'] = function (): void {
    $origin = new Bed(1, 'CAMA-FICT-A101', Bed::STATUS_OCCUPIED);
    $destination = new Bed(2, 'CAMA-FICT-A102', Bed::STATUS_AVAILABLE);
    $admission = new Admission(1, 'PAC-FICT-001', 1, Admission::STATUS_ACTIVE);

    $beds = new FakeBedRepository([$origin, $destination]);
    $admissions = new FakeAdmissionRepository([$admission]);
    $transaction = new FakeTransactionManager();

    $useCase = new TransferPatient($admissions, $beds, $transaction);
    $useCase->execute(1, 2);

    assertSameValue(Bed::STATUS_CLEANING, $origin->status(), 'La cama origen debe quedar en limpieza');
    assertSameValue(Bed::STATUS_OCCUPIED, $destination->status(), 'La cama destino debe quedar ocupada');
    assertSameValue(2, $admission->bedId(), 'La admision debe cambiar de cama');
    assertSameValue(Admission::STATUS_ACTIVE, $admission->status(), 'La admision debe continuar activa');
    assertSameValue(true, $transaction->committed, 'La transaccion debe confirmarse');
    assertSameValue(false, $transaction->rolledBack, 'No debe ejecutarse rollback');
};

$tests['regla de dominio: cama destino ocupada'] = function (): void {
    $origin = new Bed(1, 'CAMA-FICT-A101', Bed::STATUS_OCCUPIED);
    $destination = new Bed(2, 'CAMA-FICT-A102', Bed::STATUS_OCCUPIED);
    $admission = new Admission(1, 'PAC-FICT-001', 1, Admission::STATUS_ACTIVE);

    $beds = new FakeBedRepository([$origin, $destination]);
    $admissions = new FakeAdmissionRepository([$admission]);
    $transaction = new FakeTransactionManager();

    $useCase = new TransferPatient($admissions, $beds, $transaction);

    $exceptionThrown = false;

    try {
        $useCase->execute(1, 2);
    } catch (DomainException) {
        $exceptionThrown = true;
    }

    assertSameValue(true, $exceptionThrown, 'Debe rechazarse una cama destino no disponible');
    assertSameValue(false, $transaction->began, 'La transaccion no debe iniciar si falla la regla');
};

$tests['error de persistencia provoca rollback'] = function (): void {
    $origin = new Bed(1, 'CAMA-FICT-A101', Bed::STATUS_OCCUPIED);
    $destination = new Bed(2, 'CAMA-FICT-A102', Bed::STATUS_AVAILABLE);
    $admission = new Admission(1, 'PAC-FICT-001', 1, Admission::STATUS_ACTIVE);

    $beds = new FakeBedRepository([$origin, $destination]);
    $beds->failOnSave = true;

    $admissions = new FakeAdmissionRepository([$admission]);
    $transaction = new FakeTransactionManager();

    $useCase = new TransferPatient($admissions, $beds, $transaction);

    $exceptionThrown = false;

    try {
        $useCase->execute(1, 2);
    } catch (RuntimeException) {
        $exceptionThrown = true;
    }

    assertSameValue(true, $exceptionThrown, 'Debe propagarse el error de persistencia');
    assertSameValue(true, $transaction->began, 'La transaccion debe haber iniciado');
    assertSameValue(true, $transaction->rolledBack, 'Debe ejecutarse rollback');
    assertSameValue(false, $transaction->committed, 'No debe ejecutarse commit');
};

$tests['alta exitosa'] = function (): void {
    $bed = new Bed(1, 'CAMA-FICT-A101', Bed::STATUS_OCCUPIED);
    $admission = new Admission(1, 'PAC-FICT-001', 1, Admission::STATUS_ACTIVE);

    $beds = new FakeBedRepository([$bed]);
    $admissions = new FakeAdmissionRepository([$admission]);
    $transaction = new FakeTransactionManager();

    $useCase = new DischargePatient($admissions, $beds, $transaction);
    $useCase->execute(1);

    assertSameValue(Bed::STATUS_CLEANING, $bed->status(), 'La cama debe pasar a limpieza');
    assertSameValue(Admission::STATUS_CLOSED, $admission->status(), 'La admision debe cerrarse');
    assertSameValue(true, $transaction->committed, 'La transaccion debe confirmarse');
};

$passed = 0;
$failed = 0;

foreach ($tests as $name => $test) {
    try {
        $test();
        echo "[OK] {$name}" . PHP_EOL;
        $passed++;
    } catch (Throwable $exception) {
        echo "[FALLO] {$name}: {$exception->getMessage()}" . PHP_EOL;
        $failed++;
    }
}

echo PHP_EOL;
echo "Resultado: {$passed} aprobadas, {$failed} fallidas." . PHP_EOL;

exit($failed === 0 ? 0 : 1);
