<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, EventRepository $eventRepository, CategoryRepository $categoryRepository)
    {
        $title = $request->query->get('title');

        foreach (explode(' ', $title) as $word) {
            if (empty($word)) continue;
            $criteria[] = [
                'property' => 'title',
                'value' => '%' . $word . '%',
                'type' => 'like'
            ];
        }

        $criteria[] = [
            'property' => 'category',
            'value' => $request->query->get('category'),
            'type' => 'eq'
        ];
        $criteria[] = [
            'property' => 'date',
            'value1' => $request->query->get('start-date'),
            'value2' => $request->query->get('end-date'),
            'type' => 'range'
        ];
        $criteria[] = [
            'property' => 'price',
            'value1' => $request->query->get('min-price'),
            'value2' => $request->query->get('max-price'),
            'type' => 'range'
        ];

        $events = $eventRepository->findByCriteria($criteria);
        $categories = $categoryRepository->findAll();

        return $this->render('index.html.twig', [
            'events' => $events,
            'categories' => $categories
        ]);
    }
}
