<?php
/**
 * Created by PhpStorm.
 * User: Kon
 * Date: 5/19/2019
 * Time: 2:36 PM
 */

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Category;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class EventDetailsControllerTest extends WebTestCase
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

    public function testEvent()
    {
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys')->getID();
        $path = $this->client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);
        $crawler = $this->client->request('GET', $path);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Naujas renginys")')->count()
        );
    }

    public function testCommentAdd()
    {
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys')->getID();
        $path = $this->client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);
        $crawler = $this->client->request('GET', $path);

        $crawler = $this->client->click($crawler->selectLink('Add comment')->link());
       // Fill in the form and submit it
        $form = $crawler->selectButton('Submit')->form([
            'comment_form[content]' => 'TestContent'
        ]);
        $this->client->submit($form);
        // Check if user is redirected
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("TestContent")')->count());

        //Check data in the database
        $comment = self::$kernel->getContainer()->get('doctrine')->getRepository(Comment::class)->findOneByContent('TestContent');
        $this->assertEquals('TestContent', $comment->getContent());
    }

    public function testCommentEdit()
    {
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys')->getID();
        $path = $this->client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);
        $crawler = $this->client->request('GET', $path);
        $crawler = $this->client->click($crawler->selectLink('Edit')->links()[1]);
        $form = $crawler->selectButton('Submit')->form([
            'comment_form[content]' => 'TestCommentEdited'
        ]);

        $this->client->submit($form);
        // Check if user is redirected
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("TestCommentEdited")')->count());
    }

    public function testCommentDelete()
    {
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys')->getID();
        $path = $this->client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);
        $crawler = $this->client->request('GET', $path);
        $this->client->click($crawler->selectLink('Delete')->links()[1]);

        $crawler = $this->client->followRedirect();

        //Check data in the database
        $category = self::$kernel->getContainer()->get('doctrine')->getRepository(Comment::class)->findOneByContent('TestContent');
        $this->assertEmpty($category);

        // Check if the entity has been deleted from the list
        $this->assertEquals(0, $crawler->filter('html:contains("TestContent")')->count());
    }

    public function testEventDelete()
    {
        $eventRepo = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class);
        $event = $eventRepo->findOneByTitle('Naujas renginys')->getID();
        $path = $this->client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);
        $crawler = $this->client->request('GET', $path);
        $this->client->click($crawler->selectLink('Delete Event')->link());

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        $event = $eventRepo->findOneByTitle('Naujas renginys');

        $this->assertEmpty($event);

        $category = self::$kernel->getContainer()->get('doctrine')->getRepository(Category::class)->findOneByName('Sport');
        self::$kernel->getContainer()->get('doctrine')->getManager()->remove($category);
        self::$kernel->getContainer()->get('doctrine')->getManager()->flush();
    }
}