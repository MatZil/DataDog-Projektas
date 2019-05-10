<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class UserListController extends AbstractController
{

    /**
     * @Route("/admin/users", name="app_userList")
     */
    public function users()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('users/users.html.twig', array(
            'users' => $users));
    }

    /**
     * @Route("/admin/users/delete/{id}", name="app_userDelete")
     */
    public function userDelete($id)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository(User::class)->find($id);
        $comments = $manager->getRepository(Comment::class)->findBy([
           'user' => $user->getId()
        ]);
        foreach($comments as $comment){
            $comment->setUser(null);
            $manager->persist($comment);
            $manager->flush();
        }
        if ($user != null)
        {
            $manager->remove($user);
            $manager->flush();
        }

        return $this->redirectToRoute("app_userList");
    }
}
