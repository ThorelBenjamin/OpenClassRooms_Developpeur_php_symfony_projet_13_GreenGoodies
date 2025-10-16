<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 3; $i++) {
            $u = new User();
            $u->setEmail("user{$i}@example.com");
            $u->setRoles($i === 0 ? ['ROLE_ADMIN'] : ['ROLE_USER']);
            $u->setApiActivate((bool) random_int(0, 1));
            $u->setPassword($this->passwordHasher->hashPassword($u, 'password'));
            $manager->persist($u);
        }

        $manager->flush();
    }
}
