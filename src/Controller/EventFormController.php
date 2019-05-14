<?php

namespace App\Controller;


use App\Entity\Event;
use App\Entity\User;
use App\Form\EventFormType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class EventFormController extends AbstractController
{
    /**
     * @Route("/admin/{action}/event/{eventID}", name="app_eventForm")
     */
    public function createEvent(Request $request, $action, $eventID = null, \Swift_Mailer $mailer)
    {
        $entityManager = $this->getDoctrine()->getManager();

        if ($action === 'edit') {
            $event = $entityManager->getRepository(Event::class)->find($eventID);
            $oldPhoto = $event->getPhoto();
        } else {
            $event = new Event();
            $oldPhoto = null;
        }
        $users = $entityManager->getRepository(User::class)->findAll();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileSystem = new Filesystem();

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('photo')->getData();
            if ($file) {
                if ($oldPhoto != null) {
                    $fileSystem->remove($this->getParameter('photo_directory') . '/' . $oldPhoto);
                }

                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('photo_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $event->setPhoto($fileName);
            } elseif ($oldPhoto != null) {
                $event->setPhoto($oldPhoto);
            }

            if ($request->request->get('deleteCheckBox') && $event->getPhoto()) {
                $fileSystem->remove($this->getParameter('photo_directory') . '/' . $event->getPhoto());
                $event->setPhoto(null);
            }


            $entityManager->persist($event);
            $entityManager->flush();
            $category = $form->get('category')->getData();
            foreach ($users as $user) {
                $sub = $entityManager->getRepository(User::class)->find($user)->containsCategoryInSubscribedCategories($category);
                if ($sub == true) {
                    $message = (new \Swift_Message('New event has been added with your subscribed category'))
                        ->setFrom(['datasuniai@gmail.com' => 'Datašuniai'])
                        ->setTo($entityManager->getRepository(User::class)->find($user)->getEmail())
                        ->setBody(
                            $this->renderView('events/NewEventEmailForm.html.twig', [
                                'event' => $event
                            ]),
                            'text/html'
                        );
                    $mailer->send($message);
                }
            }

            return $this->redirectToRoute(($action === 'create') ? 'index' : 'app_eventDetails',
                array('eventID' => $eventID)
            );
        }

        return $this->render('events/event_form.html.twig', [
            'eventForm' => $form->createView(),
            'action' => ucfirst($action),
            'photo' => $oldPhoto
        ]);

    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
