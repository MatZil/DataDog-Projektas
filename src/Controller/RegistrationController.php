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
use App\Entity\SecurityCode;

class RegistrationController extends AbstractController
{

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator, \Swift_Mailer $mailer): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute("index");
        }

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

            $secCode = new SecurityCode();
            $secCode->setSecureCode();
            $secCode->setPurpose(SecurityCode::EMAIL_CONFIRMATION);
            $secCode->setUser($user);

            $message = (new \Swift_Message('Email Confirmation'))
                ->setFrom(['datasuniai@gmail.com' => 'Datašuniai'])
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('registration/confirmation.html.twig', [
                        'username' => $user->getUsername(),
                        'code' => $secCode->getCode()
                    ]),
                    'text/html'
                );
            $mailer->send($message);

            $entityManager->persist($secCode);
            $entityManager->flush();
            $this->addFlash('success', 'Registration succeed. Check your email inbox for an email verification link');

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

    /**
     * @Route("/confirm/{code}", name="app_confirm")
     */
    public function confirmed($code = ""): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $secCode = $entityManager->getRepository(SecurityCode::class)->findOneBy([
            'purpose' => SecurityCode::EMAIL_CONFIRMATION,
            'code' => $code
        ]);

        $user = null;

        if ($secCode != null) {
            $user = $secCode->getUser();
            $user->setIsActivated(true);
            $entityManager->remove($secCode);
            $entityManager->flush();
        }

        if ($user != null) {
            $this->addFlash('success', 'Congratulations, ' . $user->getUsername() . '! Your email has just been verified');
        } else {
            $this->addFlash('danger', 'Sorry, this email confirmation link is expired or does not exist');
        }

        return $this->render('base.html.twig');
    }
}
