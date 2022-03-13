<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;

class UserControllerTest extends WebTestCase
{
    private $client;
    private $user;
    private $admin;

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
        $this->admin = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($this->admin);
    }

    public function testPageUserWithoutUser(): void
    {
        $crawler = $this->client->request('GET', '/users');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        static::assertSame(1, $crawler->filter('input[name="username"]')->count());
        static::assertSame(1, $crawler->filter('input[name="password"]')->count());
    }

    public function testPageUserWithUser(): void
    {
        $this->loginUser();
        $this->client->request('GET', '/users');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testPageUserWithAdmin(): void
    {
        $this->loginAdmin();
        $this->client->request('GET', '/users');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', "Liste des utilisateurs");
    }

    public function testCreateUser()
    {
        $this->loginAdmin();
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        static::assertSame(1, $crawler->filter('input[name="user[username]"]')->count());
        static::assertSame(1, $crawler->filter('input[name="user[email]"]')->count());

        $form = $crawler->selectButton('Register')->form();
        $form['user[username]'] = 'Nouveau user' . rand(1, 1000);
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'nouveau@nouveau.com';


        $this->client->submit($form);

        static::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        static::assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.alert-success', "Superbe ! La l'utilisateur a été bien été ajoutée.");
    }

    public function testEditUser()
    {
        $this->loginAdmin();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['id' => $this->admin->getId() + 1]);

        $crawler = $this->client->request('GET', '/users' . '/' . $user->getId() . '/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        static::assertSame(1, $crawler->filter('input[name="user[username]"]')->count());
        static::assertSame(1, $crawler->filter('input[name="user[email]"]')->count());

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'Nouveau user modifié' . rand(1, 1000);
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'nouveau@nouveau.com';

        $this->client->submit($form);

        static::assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        static::assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.alert-success', "L'utilisateur a bien été modifié");
    }
}
