<?php

declare(strict_types=1);

namespace MicroHis\Application\UseCases;

use MicroHis\Application\Contracts\TransactionManager;
use MicroHis\Domain\Exceptions\DomainException;
use MicroHis\Domain\Repositories\AdmissionRepository;
use MicroHis\Domain\Repositories\BedRepository;
use Throwable;

final class DischargePatient
{
    public function __construct(
        private AdmissionRepository $admissions,
        private BedRepository $beds,
        private TransactionManager $transactions
    ) {
    }

    public function execute(int $admissionId): void
    {
        $admission = $this->admissions->findById($admissionId);

        if ($admission === null) {
            throw new DomainException('La admisión indicada no existe.');
        }

        if (!$admission->isActive()) {
            throw new DomainException('Solo una admisión activa puede darse de alta.');
        }

        $originBed = $this->beds->findById($admission->bedId());

        if ($originBed === null) {
            throw new DomainException('La cama de origen no existe.');
        }

        $this->transactions->begin();

        try {
            $originBed->releaseToCleaning();
            $admission->discharge();

            $this->beds->save($originBed);
            $this->admissions->save($admission);

            $this->transactions->commit();
        } catch (Throwable $error) {
            $this->transactions->rollback();

            throw $error;
        }
    }
}
