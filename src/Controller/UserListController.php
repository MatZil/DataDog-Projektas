<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class UserListController extends AbstractController
{

    /**
     * @Route("/admin/user", name="app_userList")
     */
    public function users()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('users/users.html.twig', array(
            'users' => $users));
    }

    /**
     * @Route("/admin/user/{id}/delete", name="app_userDelete")
     */
    public function userDelete($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository(User::class)->find($id);

        if ($user != null)
        {
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', 'User "' . $user->getUsername() . '" successfully deleted');
        }

        return $this->redirectToRoute("app_userList");
    }
}
