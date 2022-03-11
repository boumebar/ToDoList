<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    protected $validator;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->validator = $container->get(ValidatorInterface::class);
    }

    public function getUserEntity(): User
    {
        $user = new User;
        $user->setUsername('moi');
        $user->setEmail('moi@moi.com');
        $user->setPassword("password");
        return $user;
    }

    public function assertHasErrors(User $user, int $number)
    {
        $errors = $this->validator->validate($user);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidUser()
    {
        $this->assertHasErrors($this->getUserEntity(), 0);
    }

    public function testInvalidBlankUsername()
    {

        $user = $this->getUserEntity();
        $user->setUsername('');

        $this->assertHasErrors($user, 1);
    }

    public function testInvalidBlankEmail()
    {
        $user = $this->getUserEntity();
        $user->setEmail('');

        $this->assertHasErrors($user, 1);
    }

    public function testInvalidEmail()
    {
        $user = $this->getUserEntity();
        $user->setEmail('dfddsfdfds');

        $this->assertHasErrors($user, 1);
    }

    public function testInvalidBlankPassword()
    {
        $user = $this->getUserEntity();
        $user->setPassword('');

        $this->assertHasErrors($user, 1);
    }

    public function testDefaultUserRole()
    {
        $this->assertSame(['ROLE_USER'], $this->getUserEntity()->getRoles());
    }

    public function testGetSaltNull(): void
    {
        $user = $this->getUserEntity();
        $this->assertNull($user->getSalt());
    }

    public function testEraseCredentialsNull(): void
    {
        $user = $this->getUserEntity();
        $this->assertNull($user->eraseCredentials());
    }


    public function testAddTasks()
    {
        $task1 = new Task;
        $task2 = new Task;
        $user = $this->getUserEntity();

        $user->addTask($task1);
        $this->assertCount(1, $user->getTasks());
        $user->addTask($task2);
        $this->assertCount(2, $user->getTasks());

        $this->assertSame($task1->getAuthor(), $user);
    }

    public function testRemoveTask()
    {
        $task = new Task;
        $user = $this->getUserEntity();

        $user->addTask($task);
        $this->assertCount(1, $user->getTasks());
        $this->assertSame($task->getAuthor(), $user);
        $user->removeTask($task);
        $this->assertCount(0, $user->getTasks());
    }
}
