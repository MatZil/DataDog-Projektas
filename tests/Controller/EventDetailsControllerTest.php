<?php

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
        $client = $this->createAuthorizedClient();
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys')->getID();
        $path = $client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);
        $crawler = $client->request('GET', $path);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Naujas renginys")')->count()
        );
    }

    public function testCommentAdd()
    {
        $client = $this->createAuthorizedClient();
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys')->getID();
        $path = $client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);
        $crawler = $client->request('GET', $path);

        $crawler = $client->click($crawler->selectLink('Add comment')->link());
       // Fill in the form and submit it
        $form = $crawler->selectButton('Submit')->form([
            'comment_form[content]' => 'TestContent'
        ]);
        $client->submit($form);
        // Check if user is redirected
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("TestContent")')->count());

        //Check data in the database
        $comment = self::$kernel->getContainer()->get('doctrine')->getRepository(Comment::class)->findOneByContent('TestContent');
        $this->assertEquals('TestContent', $comment->getContent());
    }

    public function testCommentEdit()
    {
        $client = $this->createAuthorizedClient();
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys')->getID();
        $path = $client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);

        $crawler = $client->request('GET', $path);
        $crawler = $client->click($crawler->selectLink('Edit')->links()[1]);
        $form = $crawler->selectButton('Submit')->form([
            'comment_form[content]' => 'TestCommentEdited'
        ]);

        $client->submit($form);
        // Check if user is redirected
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("TestCommentEdited")')->count());
    }

    public function testCommentDelete()
    {
        $client = $this->createAuthorizedClient();
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys')->getID();
        $path = $client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);

        $crawler = $client->request('GET', $path);
        $client->click($crawler->selectLink('Delete')->links()[1]);

        $crawler = $client->followRedirect();

        //Check data in the database
        $category = self::$kernel->getContainer()->get('doctrine')->getRepository(Comment::class)->findOneByContent('TestContent');
        $this->assertEmpty($category);

        // Check if the entity has been deleted from the list
        $this->assertEquals(0, $crawler->filter('html:contains("TestContent")')->count());
    }

    public function testEventDelete()
    {
        $client = $this->createAuthorizedClient();
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys')->getID();
        $path = $client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);

        $crawler = $client->request('GET', $path);
        $client->click($crawler->selectLink('Delete Event')->link());
        $crawler = $client->followRedirect();

        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys');
        $this->assertEmpty($event);
        $this->assertEquals(0, $crawler->filter('html:contains("Nauja renginys")')->count());

        $category = self::$kernel->getContainer()->get('doctrine')->getRepository(Category::class)->findOneByName('Artistic');
        self::$kernel->getContainer()->get('doctrine')->getManager()->remove($category);
        self::$kernel->getContainer()->get('doctrine')->getManager()->flush();
    }
}
