<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Reset_PasswordFormType;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use Symfony\Component\HttpFoundation\Request;
//use App\Form\RegistrationFormType;
use App\Form\EmailFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute("index");
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error]);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route ("/resetpassword", name="app_reset")
     */
    public function resetPassword_GetEmail(Request $request, \Swift_Mailer $mailer)
    {
        $user = new User();
        $user->setUsername('dummy');
        $user->setFirstName('dummy');
        $form = $this->createForm(EmailFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $email = $form->get('email')->getData();

            $getDoctrine = $this->getDoctrine()->getManager();
            $useris = $getDoctrine->getRepository(User::class)->findOneBy(['email' => $email]);
            if (!empty($useris))
            {

                echo '<p>Verification link has been sent to this email: </p>' . $email;

                $message = (new \Swift_Message('Reset password email'))
                    ->setFrom('Datasuniai@datasuniai.com')
                    ->setTo($email)
                    ->setBody(
                        $this->renderView(
                            'emails/reset_password.html.twig',
                        ['id' => $useris->getId()]
                        ),
                        'text/html'
                    );
                $mailer->send($message);
            }
            else
                echo '<p>There is no such email registered</p>';
        }


        return $this->render('security/resetpsw_email.html.twig', [
            'emailform' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/resetPassword/{id}", name="app_changePsw")
     */
    public function resetPasswordChangePassword(Request $request, $id="", UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(Reset_PasswordFormType::class);
        $form->handleRequest($request);

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('newPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            echo '<p>Password has been changed!</p>';
            echo '<a href="/">Home Page</a>';

        }
        return $this->render('security/change_passwordByReset.html.twig', [
            'changepassform' => $form->createView(),
        ]);
    }
}
