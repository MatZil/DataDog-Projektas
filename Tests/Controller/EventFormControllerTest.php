<?php


namespace App\Tests\Controller;


use App\Entity\Category;
use App\Entity\Event;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class EventFormControllerTest extends WebTestCase
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

    public function testEventForm()
    {
        $client = $this->createAuthorizedClient();
        $container = self::$kernel->getContainer();
        $category = new Category();
        $category->setName("Sport");
        $em = $container->get('doctrine')->getManager();
        $em->persist($category);
        $em->flush();
        $crawler = $client->request('GET', '/admin/create/event');
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

        // Check if user is redirected
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('html:contains("successfully created")')->count());

        //Check data in the database
        $event = self::$kernel->getContainer()->get('doctrine')->getRepository(Event::class)->findOneByTitle('Naujas renginys');
        $this->assertEquals('Naujas renginys', $event->getTitle());

        $em->remove($em->merge($event));
        $em->remove($em->merge($category));
        $em->flush();
    }
}