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
        $rep = $repository = $this->getDoctrine()->getRepository(Event::class);
        return $this->render('events/eventDetails.html.twig', [
            'event' => $rep->findOneBySomeField($slug)
        ]);
    }



}