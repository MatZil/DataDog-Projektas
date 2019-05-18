<?php

namespace App\tests\Controller;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
        $path = $client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $client->request('GET', $path);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Categories")')->count()
        );
    }

    public function testCategoryAdd()
    {
        $client = $this->createAuthorizedClient();
        $path = $client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $client->request('GET', $path);
        $crawler = $client->click($crawler->selectLink('Add category')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Submit')->form([
            'category_form[name]' => 'TestCategory',
        ]);
        $client->submit($form);

        // Check if user is redirected
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
        $path = $client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $client->request('GET', $path);
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Submit')->form([
            'category_form[name]' => 'TestCategoryEdited',
        ]);

        $client->submit($form);

        // Check if user is redirected
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("TestCategoryEdited")')->count());
    }

    public function testCategorySubscribe()
    {
        $client = $this->createAuthorizedClient();
        $path = $client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $client->request('GET', $path);
        $client->click($crawler->selectLink('Subscribe')->link());

        $client->followRedirect();

        // Check if the button has changed to Unsubscribe
        $this->assertNotRegExp('/Subscribe/', $client->getResponse()->getContent());
        $this->assertRegExp('/Unsubscribe/', $client->getResponse()->getContent());
    }

    public function testCategoryUnsubscribe()
    {
        $client = $this->createAuthorizedClient();
        $path = $client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $client->request('GET', $path);
        $client->click($crawler->selectLink('Unsubscribe')->link());

        $client->followRedirect();

        // Check if the button has changed to Subscribe
        $this->assertNotRegExp('/Unsubscribe/', $client->getResponse()->getContent());
        $this->assertRegExp('/Subscribe/', $client->getResponse()->getContent());
    }

    public function testCategoryDelete()
    {
        $client = $this->createAuthorizedClient();
        $path = $client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $client->request('GET', $path);
        $client->click($crawler->selectLink('Delete')->link());

        $crawler = $client->followRedirect();

        //Check data in the database
        $category = self::$kernel->getContainer()->get('doctrine')->getRepository(Category::class)->findOneByName('TestCategoryEdited');
        $this->assertEmpty($category);

        // Check if the entity has been deleted from the list
        // Found exactly 1 value, because the category name is displayed in a pop up box after it is deleted
        $this->assertEquals(1, $crawler->filter('html:contains("TestCategoryEdited")')->count());
    }
}
