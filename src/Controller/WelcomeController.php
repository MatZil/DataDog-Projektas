<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(EventRepository $eventRepository)
    {
        $events = $eventRepository->findBy([], ['id' => 'DESC']);

        return $this->render('index.html.twig', [
            'events' => $events
        ]);
    }
}
