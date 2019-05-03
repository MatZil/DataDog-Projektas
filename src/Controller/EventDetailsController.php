<?php


namespace App\Controller;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EventDetailsController extends AbstractController
{
    /**
     * @Route("/events/{slug}", name="app_eventDetails")
     */
    public function event($slug = "")
    {
        $rep = $this->getDoctrine()->getRepository(Event::class);
        $event = $rep->findOneBySomeField($slug);

        if ($event == null) {
            return $this->redirectToRoute("index");
        }

        return $this->render('events/eventDetails.html.twig', [
            'event' => $event
        ]);
    }



}