<?php

namespace Tests\Controller;

use App\Entity\User;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private $client;
    private $user;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
    }

    public function loginUser(): void
    {
        $this->client->request('GET', '/login');
        $this->user = $this->userRepository->findOneBy(['username' => 'user2']);
        $this->client->loginUser($this->user);
    }

    public function loginAdmin(): void
    {
        $this->client->request('GET', '/login');
        $user = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($user);
    }

    public function testListWithUser()
    {
        $this->loginUser();
        $this->client->request('GET', '/tasks');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testListWithoutUser()
    {
        $this->client->request('GET', '/tasks');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        static::assertSame(1, $crawler->filter('input[name="username"]')->count());
        static::assertSame(1, $crawler->filter('input[name="password"]')->count());
    }

    public function testCreateTaskWithoutUser()
    {
        $this->client->request('GET', '/tasks/create');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        static::assertSame(1, $crawler->filter('input[name="username"]')->count());
        static::assertSame(1, $crawler->filter('input[name="password"]')->count());
    }


    public function testCreateTaskWithUser()
    {
        $this->loginUser();
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        static::assertSame(1, $crawler->filter('input[name="task[title]"]')->count());
        static::assertSame(1, $crawler->filter('textarea[name="task[content]"]')->count());

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Une tache',
            'task[content]' => 'Un contenu'
        ]);

        $this->client->submit($form);
        static::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', "Superbe ! La tâche a été bien été ajoutée.");
    }

    public function testEditTaskWithGoodUser()
    {
        $this->loginUser();
        $task = static::getContainer()->get(TaskRepository::class)->findOneBy(['author' => $this->user->getId()]);
        $crawler = $this->client->request('GET', "/tasks" . "/" . $task->getId() . "/edit");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Une tache modifiée',
            'task[content]' => 'Un contenu modifié'
        ]);

        $this->client->submit($form);
        static::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', "Superbe ! La tâche a bien été modifiée.");
    }

    public function testEditTaskWithBadUser()
    {
        $this->loginUser();
        $this->client->request('GET', "/tasks/260/edit");
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.break-long-words', "Seul le propriétaire peut effectuer ce changement");
    }

    public function testToggle()
    {
        $this->loginUser();
        $task = static::getContainer()->get(TaskRepository::class)->findOneBy(['author' => $this->user->getId()]);
        $this->client->request('GET', '/tasks' . '/' . $task->getId() . '/toggle');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.alert-success', "Superbe ! La tâche " . $task->getTitle() . " a bien été marquée comme faite.");
    }

    public function testDeleteWithBadUser()
    {
        $this->loginUser();
        $task = static::getContainer()->get(TaskRepository::class)->findOneBy(['author' => "18"]);
        $this->client->request('GET', '/tasks' . '/' . $task->getId() . '/delete');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteWithGoodUser()
    {
        $this->loginUser();
        $task = static::getContainer()->get(TaskRepository::class)->findOneBy(['author' => $this->user->getId()]);
        $this->client->request('GET', '/tasks' . '/' . $task->getId() . '/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.alert-success', "Superbe ! La tâche a bien été supprimée.");
    }

    public function testAdminDeleteAnonymousTask()
    {
        $this->loginAdmin();
        $task = static::getContainer()->get(TaskRepository::class)->findOneBy(['author' => null]);
        $this->client->request('GET', '/tasks' . '/' . $task->getId() . '/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.alert-success', "Superbe ! La tâche a bien été supprimée.");
    }
}
