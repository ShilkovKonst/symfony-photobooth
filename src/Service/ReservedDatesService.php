<?php

namespace App\Service;

use DateTime;
use DateTimeZone;
use App\Repository\ReservedDatesRepository;

class ReservedDatesService
{
    private $currentDate;
    private $reservedDatesRepo;

    public function __construct(ReservedDatesRepository $reservedDatesRepo)
    {
        $this->currentDate = new DateTime();
        $this->reservedDatesRepo = $reservedDatesRepo;
    }

    public function getMinDate() 
    {
        $this->currentDate->setTimezone(new DateTimeZone('Europe/Paris'));

        return $this->currentDate->modify('+2 days')->format('Y-m-d');
    }

    public function getActualDates() 
    {
        $minDate = $this->getMinDate();
        $reservedDatesRaw = $this->reservedDatesRepo->findAll();
        $reservedDates = array_map(function ($date) {
            return $date->getDates();      
        }, $reservedDatesRaw);
        $reservedDates = array_merge(...$reservedDates);

        return array_filter($reservedDates, function ($item) use ($minDate) { return $item >= $minDate; });
    }
}