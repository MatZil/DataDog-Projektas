<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class UserListController extends AbstractController
{

    /**
     * @Route("/users", name="app_userList")
     */
    public function users()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('users/users.html.twig', array(
            'users' => $users));
    }

    /**
     * @Route("/users/delete/{id}", name="app_userDelete")
     */
    public function userDelete($id)
    {
        $users = $this->getDoctrine()->getEntityManager();
        $user = $users->getRepository(User::class)->find($id);

        $users->remove($user);
        $users->flush();

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('users/users.html.twig', array(
            'users' => $users));
    }
}
