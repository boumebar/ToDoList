<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $taskAnonyme = new Task();

        $admin->setUsername("admin")
            ->setEmail("admin@admin.fr")
            ->setPassword($this->hasher->hashPassword($admin, 'password'))
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        $taskAnonyme->setAuthor(null)
            ->setTitle('titre anonyme');
        $taskAnonyme->setContent("contenu anonyme");
        $manager->persist($taskAnonyme);


        for ($i = 1; $i <= 3; $i++) {
            $user = new User();

            $user->setUsername("user$i")
                ->setEmail("user$i@user$i.com")
                ->setPassword($this->hasher->hashPassword($user, 'password'))
                ->setRoles(["ROLE_USER"]);
            $manager->persist($user);

            for ($j = 1; $j <= 3; $j++) {
                $task = new Task();

                $task->setTitle("A faire user $i");
                $task->setContent("Description de la tache $j ");
                $task->setAuthor($user);
                $manager->persist($task);
            }
        }

        $manager->flush();
    }
}
