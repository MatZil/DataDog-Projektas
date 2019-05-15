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
     * @Route("/events/{eventID}", name="app_eventDetails")
     */
    public function event($eventID)
    {
        $rep = $this->getDoctrine()->getRepository(Event::class);
        $event = $rep->findOneBySomeField($eventID);

        if ($event == null) {
            return $this->redirectToRoute("index");
        }
        return $this->render('events/eventDetails.html.twig', [
            'event' => $event
        ]);
    }

    /**
     * @Route("/events/{eventID}/add_comment", name="app_addComment")
     */
    public function addComment(Request $request, $eventID){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $rep = $this->getDoctrine()->getRepository(Event::class);
            $event = $rep->findOneBySomeField($eventID);
            $user = $this->getUser();

            $comment->setEvent($event);
            $comment->setUser($user);

            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Comment added');

            return $this->redirectToRoute('app_eventDetails', [
                'eventID' => $eventID]);
        }

        return $this->render('events/comment_form.html.twig', [
            'addcommentform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/events/{eventID}/delete_comment/{commentID}", name="app_deleteComment")
     */
    public function commentDelete($commentID, $eventID){
        $manager = $this->getDoctrine()->getManager();
        $comment = $manager->getRepository(Comment::class)->find($commentID);
        if ($comment != null) {
            $manager->remove($comment);
            $manager->flush();
            $this->addFlash('success', 'Comment deleted');
        }
        return $this->redirectToRoute('app_eventDetails', [
            'eventID' => $eventID]);
    }

    /**
     * @Route("/admin/events/{eventID}/delete", name="app_deleteEvent")
     */
    public function eventDelete($eventID){
        $manager = $this->getDoctrine()->getManager();
        $event = $manager->getRepository(Event::class)->find($eventID);
        if ($event != null){
            if($event->getPhoto()){
                $fileSystem = new Filesystem();
                $fileSystem->remove($this->getParameter('photo_directory').'/'.$event->getPhoto());
            }
            $manager->remove($event);
            $manager->flush();
            $this->addFlash('success', 'Event deleted');
        }
        return $this->redirectToRoute('index');
    }




}