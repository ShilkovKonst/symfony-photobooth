<?php

namespace App\Service;

use App\Entity\Machine;
use App\Repository\MachineRepository;

class MachinesReservationsService
{
    private $machineRepo;

    public function __construct(MachineRepository $machineRepo)
    {
        $this->machineRepo = $machineRepo;
    }

    public function checkMachinesDates($eventDate)
    {
        $machines = $this->machineRepo->findBy(['isAvailable' => true]);
        foreach ($machines as $machine) {
            /** @var Machine $machine */
            if (count($machine->getReservations()) === 0) {
                return $machine;
            }
        }
        foreach ($machines as $machine) {
            /** @var Machine $machine */
            $reservedDatesRaw = array_map(function ($dates) {
                return $dates->getDates();
            }, $machine->getReservedDates()->toArray());

            $reservedDates = array_merge(...$reservedDatesRaw);
            if (!in_array($eventDate, $reservedDates)) {
                return $machine;
            }
        }

        return null;
    }
}
