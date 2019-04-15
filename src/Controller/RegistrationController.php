<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator, \Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $message = (new \Swift_Message('My Subject'))
                ->setFrom(['datasuniai@gmail.com' => 'Datašuniai'])
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('registration/confirmation.html.twig', [
                        'username' => $user->getUsername()
                    ]),
                    'text/html'
                );
            $mailer->send($message);
            //$this->CreateEvent($user->getUsername()); //This creates the event that deletes the user after 1 week. Commented out for testing convenience, if you decide to uncomment it make sure you enable confirmation e-mails in config/packages/swiftmailer.yaml

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $formAuthenticator,
                'main'
            );
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function CreateEvent($username){
        $em = $this->getDoctrine()->getManager();

        $RAW_QUERY = "CREATE EVENT {$username}_DELETE
                      ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 1 WEEK
                      DO
                         DELETE FROM user
                         WHERE username = '{$username}';";

        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();
    }
}
