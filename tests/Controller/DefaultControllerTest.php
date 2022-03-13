<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testIndex()
    {
        $this->client->request('GET', '/');

        $this->assertResponseRedirects('/login');

        $crawler = $this->client->followRedirect();
        static::assertSame(1, $crawler->filter('input[name="username"]')->count());
        static::assertSame(1, $crawler->filter('input[name="password"]')->count());
    }
}
