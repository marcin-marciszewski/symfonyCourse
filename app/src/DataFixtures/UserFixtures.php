<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
// private UserPasswordEncoderInterface $passwordEncoder
class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$name, $lastName, $email, $password, $apiKey, $roles]) {
            $user = new User;
            $user->setName($name);
            $user->setLastName($lastName);
            $user->setEmail($email);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setName($name);
            $user->setVimeoApiKey($apiKey);
            $user->setRoles($roles);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getUserData(): array
    {
        return [
            ['Mike', 'Smimth', 'aa@aa.pl', 'pass', 'asdfasas', ['ROLE_ADMIN']],
            ['Mike2', 'Smimth2', 'aa2@aa.pl', 'pass', null, ['ROLE_ADMIN']],
            ['Mike3', 'Smimth3', 'aa3@aa.pl', 'pass', null, ['ROLE_USER']]
        ];
    }
}
