<?php

namespace Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{


    private $client;
    private $userRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }


    public function loginUser(): void
    {
        $this->client->request('GET', '/login');
        $this->user = $this->userRepository->findOneBy(['username' => 'user2']);
        $this->client->loginUser($this->user);
    }


    public function testPageLogin()
    {
        $this->client->request('GET', '/login');
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }


    public function testValidLoginForm()
    {
        $crawler = $this->client->request('GET', '/login');

        static::assertSame(1, $crawler->filter('input[name="username"]')->count());
        static::assertSame(1, $crawler->filter('input[name="password"]')->count());

        $this->loginUser();
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
    }

    public function testLoginWithWrongCredentials()

    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form([
            'username' => 'BadUsername',
            'password' => 'BadPassword'
        ]);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-danger', "Invalid credentials.");
    }

    public function testLoggedAskPageLogin()
    {
        $this->loginUser();

        $this->client->request('GET', '/login');
        $this->assertResponseRedirects('/');
    }

    public function testSuccessLogout()
    {
        $this->loginUser();

        $this->client->request('GET', '/logout');
        $this->assertResponseRedirects('http://localhost/login', 302);
    }
}
