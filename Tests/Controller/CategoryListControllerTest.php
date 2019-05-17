<?php

namespace App\tests\Controller;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Form\CategoryFormType;


class CategoryListControllerTest extends WebTestCase
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

    public function testCategories()
    {
        $client = self::createClient();
        $client->request('GET', '/categories');
        /** Returns code 302, because user is not logged in and is redirected to index page */
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        /** Login admin with username: admin */
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/categories');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Categories")')->count()
        );
    }

    public function testCategoryAdd()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/categories');
        $crawler = $client->click($crawler->selectLink('Add category')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Submit')->form([
            'category_form[name]' => 'TestCategory',
        ]);
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("TestCategory")')->count());

        //Check data in the database
        $category = self::$kernel->getContainer()->get('doctrine')->getRepository(Category::class)->findOneByName('TestCategory');
        $this->assertEquals('TestCategory', $category->getName());
    }

    public function testCategoryEdit()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/categories');
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        // Edit the entity
        //$crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Submit')->form([
            'category_form[name]' => 'TestCategoryEdited',
        ]);

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("TestCategoryEdited")')->count());
    }

    public function testCategoryDelete()
    {
        $client = $this->createAuthorizedClient();
        $crawler = $client->request('GET', '/categories');
        $crawler = $client->click($crawler->selectLink('Delete')->link());

        // Delete the entity
        //$client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        //Check data in the database
        $category = self::$kernel->getContainer()->get('doctrine')->getRepository(Category::class)->findOneByName('TestCategoryEdited');
        $this->assertEmpty($category);

        // Check the entity has been delete on the list
        // Found exactly 1 value, because the category name is displayed in a pop up box after it is deleted
        $this->assertEquals(1, $crawler->filter('html:contains("TestCategoryEdited")')->count());
    }
}
