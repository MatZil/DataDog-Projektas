<?php

namespace App\tests\Controller;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class CategoryListControllerTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = $this->createAuthorizedClient();
    }

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
        $this->client = self::createClient();
        $path = $this->client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $this->client->request('GET', $path);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Categories")')->count()
        );
    }

    public function testCategoryAdd()
    {
        $path = $this->client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $this->client->request('GET', $path);
        $crawler = $this->client->click($crawler->selectLink('Add category')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Submit')->form([
            'category_form[name]' => 'TestCategory',
        ]);
        $this->client->submit($form);

        // Check if user is redirected
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("TestCategory")')->count());

        //Check data in the database
        $category = self::$kernel->getContainer()->get('doctrine')->getRepository(Category::class)->findOneByName('TestCategory');
        $this->assertEquals('TestCategory', $category->getName());
    }

    public function testCategoryEdit()
    {
        $path = $this->client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $this->client->request('GET', $path);
        $crawler = $this->client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Submit')->form([
            'category_form[name]' => 'TestCategoryEdited',
        ]);

        $this->client->submit($form);

        // Check if user is redirected
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("TestCategoryEdited")')->count());
    }

    public function testCategorySubscribe()
    {
        $path = $this->client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $this->client->request('GET', $path);
        $this->client->click($crawler->selectLink('Subscribe')->link());

        $this->client->followRedirect();

        // Check if the button has changed to Unsubscribe
        $this->assertNotRegExp('/Subscribe/', $this->client->getResponse()->getContent());
        $this->assertRegExp('/Unsubscribe/', $this->client->getResponse()->getContent());
    }

    public function testCategoryUnsubscribe()
    {
        $path = $this->client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $this->client->request('GET', $path);
        $this->client->click($crawler->selectLink('Unsubscribe')->link());

        $this->client->followRedirect();

        // Check if the button has changed to Subscribe
        $this->assertNotRegExp('/Unsubscribe/', $this->client->getResponse()->getContent());
        $this->assertRegExp('/Subscribe/', $this->client->getResponse()->getContent());
    }

    public function testCategoryDelete()
    {
        $path = $this->client->getContainer()->get('router')->generate('app_categoryList', [], false);
        $crawler = $this->client->request('GET', $path);
        $this->client->click($crawler->selectLink('Delete')->link());

        $crawler = $this->client->followRedirect();

        //Check data in the database
        $category = self::$kernel->getContainer()->get('doctrine')->getRepository(Category::class)->findOneByName('TestCategoryEdited');
        $this->assertEmpty($category);

        // Check if the entity has been deleted from the list
        // Found exactly 1 value, because the category name is displayed in a pop up box after it is deleted
        $this->assertEquals(1, $crawler->filter('html:contains("TestCategoryEdited")')->count());
    }
}
