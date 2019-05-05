<?php


namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use App\Form\CommentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class EventDetailsController extends AbstractController
{
    /**
     * @Route("/events/{slug}", name="app_eventDetails")
     */
    public function event($slug)
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

    /**
     * @Route("/events/{slug}/add_comment", name="app_addComment")
     */
    public function addComment(Request $request, $slug)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $rep = $this->getDoctrine()->getRepository(Event::class);
            $event = $rep->findOneBySomeField($slug);
            $user = $this->getUser();

            $comment->setEvent($event);
            $comment->setUser($user);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_eventDetails', [
                'slug' => $slug]);
        }

        return $this->render('events/comment_form.html.twig', [
            'addcommentform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/events/{eventId}/delete_comment/{commentId}", name="app_deleteComment")
     */
    public function commentDelete($commentId, $eventId)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $manager = $this->getDoctrine()->getManager();
        $comment = $manager->getRepository(Comment::class)->find($commentId);

        if ($comment != null)
        {
            $manager->remove($comment);
            $manager->flush();
        }

        return $this->redirectToRoute('app_eventDetails', [
            'slug' => $eventId]);
    }



}