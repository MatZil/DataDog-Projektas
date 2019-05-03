<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConfirmedController extends AbstractController
{
    /**
     * @Route("/confirmed/{slug}", name="app_confirmed")
     */
    public function confirmed($slug = "") : Response
    {
        $this->DeleteEvent($slug);
        return $this->render('registration/confirmed.html.twig',[
            'username' => $slug
        ]);
    }

    private function DeleteEvent($username){
        $em = $this->getDoctrine()->getManager();

        $RAW_QUERY = "DROP EVENT {$username}_DELETE";

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();
    }
}