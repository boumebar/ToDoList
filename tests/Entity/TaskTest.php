<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskTest extends KernelTestCase
{
    protected $validator;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->validator = $container->get(ValidatorInterface::class);
    }

    public function getTaskEntity(): Task
    {
        $task = new Task;
        $task->setTitle('Nouveau titre');
        $task->setContent('nouvelle description');
        return $task;
    }

    public function assertHasErrors(Task $task, int $number)
    {
        $errors = $this->validator->validate($task);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidTask()
    {
        $this->assertHasErrors($this->getTaskEntity(), 0);
    }

    public function testGetId()
    {
        $task = new Task();
        static::assertEquals($task->getId(), null);
    }

    public function testInvalidBlankTitle()
    {

        $task = $this->getTaskEntity();
        $task->setTitle('');

        $this->assertHasErrors($task, 1);
    }

    public function testInvalidBlankContent()
    {
        $task = $this->getTaskEntity();
        $task->setContent('');

        $this->assertHasErrors($task, 1);
    }
    public function testCanAuthorNull()
    {
        $this->assertHasErrors($this->getTaskEntity()->setAuthor(null), 0);
    }

    public function testIsDoneIsFalse()
    {
        $this->assertEquals(false, $this->getTaskEntity()->isDone());
    }

    public function testToogleisDoneTrue()
    {
        $task = new Task();
        $task->toggle(true);
        $this->assertEquals(true, $task->isDone());
    }
}
