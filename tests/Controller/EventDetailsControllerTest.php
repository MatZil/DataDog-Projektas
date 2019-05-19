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

    public function testCreateTestEvent()
    {
        $client = $this->createAuthorizedClient();
        $container = self::$kernel->getContainer();
        $category = new Category();
        $category->setName("Artistic");
        $em = $container->get('doctrine')->getManager();
        $em->persist($category);
        $em->flush();
        $path = $client->getContainer()->get('router')->generate('app_eventForm', ['action' => 'create'], false);
        $crawler = $client->request('GET', $path);
        // Fill in the form and submit it
        $form = $crawler->selectButton('Create Event')->form([
            'event_form[title]' => 'Naujas renginys',
            'event_form[intro]' => 'Naujo renginio izanga',
            'event_form[description]' => 'Kiek ilgokas naujo renginio aprasymas, i kuri ieina ganetinai nemazai smulkmenu.',
            'event_form[date][date][year]' => 2020,
            'event_form[date][date][month]' => 05,
            'event_form[date][date][day]' => 01,
            'event_form[date][time][hour]' => 12,
            'event_form[date][time][minute]' => 00,
            'event_form[location]' => 'Santakos slenis',
            'event_form[category]' => $category->getId(),
            'event_form[price]' => 12
        ]);
        $client->submit($form);
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys');
        $this->assertEquals('Naujas renginys', $event->getTitle());

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
/**
    public function testCommentEdit()
    {
        $client = $this->createAuthorizedClient();
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys')->getID();
        $path = $client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);
        $crawler = $client->request('GET', $path);
        $crawler = $client->click($crawler->selectLink('Edit')->link());

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
    /**
    public function testCommentDelete()
    {
        $client = $this->createAuthorizedClient();
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys')->getID();
        $path = $client->getContainer()->get('router')->generate('app_eventDetails', ['eventID'=>$event], false);
        $crawler = $client->request('GET', $path);
        $client->click($crawler->selectLink('Delete')->link());

        $crawler = $client->followRedirect();

        //Check data in the database
        $category = self::$kernel->getContainer()->get('doctrine')->getRepository(Comment::class)->findOneByContent('TestContent');
        $this->assertEmpty($category);

        // Check if the entity has been deleted from the list
        $this->assertEquals(0, $crawler->filter('html:contains("TestContent")')->count());
    }
     **/

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