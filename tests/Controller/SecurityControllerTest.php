<?php

namespace App\tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityControllerTest extends WebTestCase
{

    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $container = static::$kernel->getContainer();
        $session = $container->get('session');
        $person = self::$kernel->getContainer()->get('doctrine')->getRepository(User::class)->findOneByUsername('deleteme');

        $token = new UsernamePasswordToken($person, null, 'main', $person->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    public function testLogin()
    {
        $client = $this->createClient();
        $path = $client->getContainer()->get('router')->generate('index', [], false);
        $crawler = $client->request('GET', $path);

        $crawler = $client->click($crawler->selectLink('Login')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Sign in')->form([
            'login' => 'testemail@datasuniai.com',
            'password' => '123456'
        ]);
        $client->submit($form);

        // Check if user is redirected
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('.navbar:contains("Test")')->count());
    }

    public function testChangePassword()
    {
        $client = $this->createAuthorizedClient();
        $path = $client->getContainer()->get('router')->generate('app_changePassword', [], false);
        $crawler = $client->request('GET', $path);

        // Fill in the form and submit it
        $form = $crawler->selectButton('Change')->form([
            'change_password_form[CurrentPassword]' => '123456',
            'change_password_form[NewPassword][first]' => '12345678',
            'change_password_form[NewPassword][second]' => '12345678'
        ]);
        $client->submit($form);

        // Check if user is redirected
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('.alert:contains("Password successfully changed")')->count());
    }

    public function testLogout()
    {
        $client = $this->createAuthorizedClient();
        $path = $client->getContainer()->get('router')->generate('index', [], false);
        $crawler = $client->request('GET', $path);

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('.navbar:contains("Test")')->count());

        $crawler = $client->click($crawler->selectLink('Logout')->link());

        // Check if user is redirected
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('.navbar:contains("Login")')->count());
    }
}
