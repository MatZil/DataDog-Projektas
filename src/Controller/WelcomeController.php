<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(UserInterface $user = null)
    {

        return $this->render('index.html.twig', [
            'user' => $user,
        ]);
    }
}
