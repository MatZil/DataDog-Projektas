<?php

namespace App\tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserListControllerTest extends WebTestCase
{
    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(User::class)->findOneByUsername('admin');

        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    public function testUsers()
    {
        $client = self::createClient();
        $path = $client->getContainer()->get('router')->generate('app_userList', [], false);
        $client->request('GET', $path);
        // Returns code 302, because user is not logged in or is not an admin
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Login admin with username: admin (has to be registered to the website with admin privileges)
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', $path);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Registered users")')->count()
        );
    }

    public function testUserDelete()
    {
        $client = $this->createAuthorizedClient();
        $path = $client->getContainer()->get('router')->generate('app_userList', [], false);
        $crawler = $client->request('GET', $path);
        $client->click($crawler->selectLink('Delete')->eq(1)->link()); // Select second link

        $crawler = $client->followRedirect();

        //Check data in the database
        $user = self::$kernel->getContainer()->get('doctrine')->getRepository(User::class)->findOneByUsername('deleteme');
        $this->assertEmpty($user);

        // Check if the entity has been deleted from the list
        // Found exactly 1 value, because the username is displayed in a pop up box after it is deleted
        $this->assertEquals(1, $crawler->filter('html:contains("deleteme")')->count());
    }
}
