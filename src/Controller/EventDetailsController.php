<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use App\Form\CommentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventDetailsController extends AbstractController
{
    /**
     * @Route("/event/{eventID}", name="app_eventDetails")
     */
    public function event($eventID)
    {
        $event = $this->getDoctrine()->getRepository(Event::class)->find($eventID);

        if ($event == null) {
            return $this->redirectToRoute("index");
        }

        return $this->render('events/eventDetails.html.twig', [
            'event' => $event
        ]);
    }

    /**
     * @Route("/event/{eventID}/comment/add", name="app_commentAdd")
     */
    public function addComment(Request $request, $eventID)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $event = $this->getDoctrine()->getRepository(Event::class)->find($eventID);
            $user = $this->getUser();

            if ($event != null) {
                $comment->setEvent($event);
                $comment->setUser($user);

                $entityManager->persist($comment);
                $entityManager->flush();
                $this->addFlash('success', 'Comment successfully added');
            }

            return $this->redirectToRoute('app_eventDetails', [
                'eventID' => $eventID
            ]);
        }

        return $this->render('events/comment_form.html.twig', [
            'addcommentform' => $form->createView(),
        ]);
    }


    /**
     * @Route("/event/{eventID}/comment/{commentID}/reply", name="app_commentReply")
     */
    public function replyComment(Request $request, $eventID, $commentID)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $parent = $this->getDoctrine()->getRepository(Comment::class)->find($commentID);
            $user = $this->getUser();

            if ($parent != null) {
                $comment->setParentComment($parent);
                $comment->setUser($user);

                $entityManager->persist($comment);
                $entityManager->flush();
                $this->addFlash('success', 'Reply successfully added');
            }

            return $this->redirectToRoute('app_eventDetails', [
                'eventID' => $eventID
            ]);
        }

        return $this->render('events/comment_form.html.twig', [
            'addcommentform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/event/{eventID}/comment/{commentID}/edit", name="app_commentEdit")
     */
    public function editComment(Request $request, $eventID, $commentID)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $entityManager = $this->getDoctrine()->getManager();
        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($commentID);

        $user = $this->getUser();
        if ($comment != null && ($user === $comment->getUser() || $this->isGranted('ROLE_ADMIN'))) {
        } else {
            return $this->redirectToRoute('app_eventDetails', [
                'eventID' => $eventID
            ]);
        }

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Reply successfully updated');

            return $this->redirectToRoute('app_eventDetails', [
                'eventID' => $eventID
            ]);
        }

        return $this->render('events/comment_form.html.twig', [
            'addcommentform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/event/{eventID}/comment/{commentID}/delete", name="app_commentDelete")
     */
    public function commentDelete($commentID, $eventID)
    {
        $manager = $this->getDoctrine()->getManager();
        $comment = $manager->getRepository(Comment::class)->find($commentID);
        if ($comment != null) {
            $manager->remove($comment);
            $manager->flush();
            $this->addFlash('success', 'Comment successfully deleted');
        }
        return $this->redirectToRoute('app_eventDetails', [
            'eventID' => $eventID
        ]);
    }

    /**
     * @Route("/admin/event/{eventID}/delete", name="app_eventDelete")
     */
    public function eventDelete($eventID)
    {
        $manager = $this->getDoctrine()->getManager();
        $event = $manager->getRepository(Event::class)->find($eventID);
        if ($event != null) {
            if ($event->getPhoto()) {
                $fileSystem = new Filesystem();
                $fileSystem->remove($this->getParameter('photo_directory') . '/' . $event->getPhoto());
            }
            $manager->remove($event);
            $manager->flush();
            $this->addFlash('success', 'Event "' . $event->getTitle() . '" successfully deleted');
        }
        return $this->redirectToRoute('index');
    }
}
