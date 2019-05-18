<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WelcomeControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $path = $client->getContainer()->get('router')->generate('index', [], false);
        $crawler = $client->request('GET', $path);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Upcoming Events")')->count()
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Login")')->count()
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Register")')->count()
        );
    }
}
