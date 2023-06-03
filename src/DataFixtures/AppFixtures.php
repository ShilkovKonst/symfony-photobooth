<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Entity\Machine;
use App\Entity\Reservation;
use App\Entity\ReservedDates;
use App\Repository\UserRepository;
use App\Repository\MachineRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\ReservationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $date;
    private $userRep;
    private $machineRep;
    private $reservationRep;

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRep,
        MachineRepository $machineRep,
        ReservationRepository $reservationRep
    ) {
        $this->date = new DateTime();
        $this->userRep = $userRep;
        $this->machineRep = $machineRep;
        $this->reservationRep = $reservationRep;
    }

    public function load(ObjectManager $manager): void
    {
        // create users
        $this->createUser(
            $manager,
            'admin@users.com',
            '123456789',
            ['ROLE_ADMIN'],
            'Admin',
            'Adminovich',
            '+123456789',
            '00000',
            'City 0',
            'Street 0',
            'Build No.0'
        );
        for ($i = 1; $i < 4; $i++) {
            $this->createUser(
                $manager,
                $i . 'user@users.com',
                '123456789',
                [],
                $i . 'User',
                $i . 'Userovich',
                $i . '+123456789',
                $i . '0000',
                'City ' . $i,
                'Street ' . $i,
                'Build No.' . $i
            );
        }

        //create machines
        for ($i = 1; $i < 5; $i++) {
            $this->createMachine(
                $manager,
                'Machine' . $i,
                'Description' . $i,
                $i % 2 == 0
            );
        }
        $manager->flush();

        //create reservations
        for ($i = 1; $i < 5; $i++) {
            $this->createReservation(
                $manager,
                new DateTime('2023-06-'.$i*3),
                $this->userRep->findOneByEmail('1user@users.com'),
                $i . 'event',
                '',
                true,
                '9400' . $i,
                $i . 'City',
                $i . 'Address',
                '',
                $this->machineRep->findOneByName('Machine' . $i),
                'price_1N1Q6dLZMRT8NLs8vZ0ihFQG'
            );
        }
        $manager->flush();

        //create reservedDates
        for ($i = 1; $i <= count($this->reservationRep->findAll()); $i++) {
            $date = $this->reservationRep->findOneByEventType($i . 'event')->getEventDate();
            $dates = [
                date_format($date->modify('-1 days'), 'Y-m-d'),
                date_format($date->modify('+1 days'), 'Y-m-d'),
                date_format($date->modify('+1 days'), 'Y-m-d')
            ];
            $this->createReservedDates(
                $manager,
                $this->machineRep->findOneByName('Machine' . $i),
                $this->reservationRep->findOneByEventType($i . 'event'),
                $dates
            );
        }
        $manager->flush();
    }

    private function createUser(
        ObjectManager $manager,
        $email,
        $password,
        array $role,
        $firstName,
        $lastName,
        $telMob,
        $zipCode,
        $city,
        $street,
        $buildNumber
    ) {
        $user = new User;
        $user->setEmail($email);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setRoles($role);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setMobTel($telMob);
        $user->setZipCode($zipCode);
        $user->setCity($city);
        $user->setStreet($street);
        $user->setBuildNumber($buildNumber);

        $manager->persist($user);
    }

    private function createMachine(
        ObjectManager $manager,
        $name,
        $description,
        $isAvailable
    ) {
        $machine = new Machine;
        $machine->setName($name);
        $machine->setDescription($description);
        $machine->setIsAvailable($isAvailable);

        $manager->persist($machine);
    }

    private function createReservation(
        ObjectManager $manager,
        $eventDate,
        $user,
        $eventType,
        $addEventType,
        $isTermsAccepted,
        $eventZip,
        $eventCity,
        $eventAddress,
        $eventAddressAddInfo,
        $machine,
        $eventPlan
    ) {
        $reservation = new Reservation;
        $reservation->setUser($user);
        $reservation->setEventDate($eventDate);
        $reservation->setEventType($eventType);
        $reservation->setAddEventType($addEventType);
        $reservation->setIsTermsAccepted($isTermsAccepted);
        $reservation->setEventZip($eventZip);
        $reservation->setEventCity($eventCity);
        $reservation->setEventAddress($eventAddress);
        $reservation->setEventAddressAddInfo($eventAddressAddInfo);
        $reservation->setMachine($machine);
        $reservation->setEventPlan($eventPlan);

        $manager->persist($reservation);
    }

    private function createReservedDates(
        ObjectManager $manager,
        $machine,
        $reservation,
        $dates
    ) {
        $reservedDates = new ReservedDates;
        $reservedDates->setMachine($machine);
        $reservedDates->setReservation($reservation);
        $reservedDates->setDates($dates);

        $manager->persist($reservedDates);
    }
}
