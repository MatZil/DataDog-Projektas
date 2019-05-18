<?php

namespace App\tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = $this->createClient();
        $path = $client->getContainer()->get('router')->generate('app_register', [], false);
        $crawler = $client->request('GET', $path);
        //$crawler = $client->click($crawler->selectLink('Add category')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Register')->form([
            'registration_form[email]' => 'testemail@datasuniai.com',
            'registration_form[username]' => 'deleteme',
            'registration_form[plainPassword][first]' => '123456',
            'registration_form[plainPassword][second]' => '123456',
            'registration_form[firstName]' => 'Test',
            'registration_form[surname]' => 'Testington',
        ]);
        $client->submit($form);

        // Check if user is redirected
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Registration succeed")')->count());

        //Check data in the database
        $user = self::$kernel->getContainer()->get('doctrine')->getRepository(User::class)->findOneByUsername('deleteme');
        $this->assertEquals('deleteme', $user->getUsername());
    }
}