<?php

declare(strict_types=1);

namespace MicroHis\Application\UseCases;

use MicroHis\Application\Contracts\TransactionManager;
use MicroHis\Domain\Exceptions\DomainException;
use MicroHis\Domain\Repositories\AdmissionRepository;
use MicroHis\Domain\Repositories\BedRepository;
use Throwable;

final class TransferPatient
{
    public function __construct(
        private AdmissionRepository $admissions,
        private BedRepository $beds,
        private TransactionManager $transactions
    ) {
    }

    public function execute(int $admissionId, int $destinationBedId): void
    {
        $admission = $this->admissions->findById($admissionId);

        if ($admission === null) {
            throw new DomainException('La admisión indicada no existe.');
        }

        if (!$admission->isActive()) {
            throw new DomainException('Solo una admisión activa puede trasladarse.');
        }

        $originBed = $this->beds->findById($admission->bedId());

        if ($originBed === null) {
            throw new DomainException('La cama de origen no existe.');
        }

        $destinationBed = $this->beds->findById($destinationBedId);

        if ($destinationBed === null) {
            throw new DomainException('La cama destino no existe.');
        }

        if ($destinationBedId === $originBed->id()) {
            throw new DomainException('La cama destino debe ser distinta de la cama de origen.');
        }

        if (!$destinationBed->isAvailable()) {
            throw new DomainException('La cama destino no está disponible.');
        }

        $this->transactions->begin();

        try {
            $originBed->releaseToCleaning();
            $destinationBed->occupy();
            $admission->transferToBed($destinationBedId);

            $this->beds->save($originBed);
            $this->beds->save($destinationBed);
            $this->admissions->save($admission);

            $this->transactions->commit();
        } catch (Throwable $error) {
            $this->transactions->rollback();

            throw $error;
        }
    }
}
