<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Machine;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
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

        for ($i = 1; $i < 5; $i++) {
            $this->createMachine(
                $manager,
                'Machine' . $i,
                'Description' . $i,
                $i % 2 == 0
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
}
