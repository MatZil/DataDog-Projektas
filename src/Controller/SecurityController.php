<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Change_PasswordFormType;
use App\Form\Reset_PasswordFormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route ("/reset", name="app_reset")
     */
    public function resetPassword_GetEmail(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(EmailFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $email = $form->get('email')->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user != null) {

                $this->addFlash('success', 'link has been sent to this email: ' . $email);

                $message = (new \Swift_Message('Reset password email'))
                    ->setFrom('Datasuniai@datasuniai.com')
                    ->setTo($email)
                    ->setBody(
                        $this->renderView(
                            'security/reset_password.html.twig',
                            ['id' => $user->getId()]),
                        'text/html'
                    );
                $mailer->send($message);
            } else {
                $emailError = new FormError("There is no such email registered");
                $form->get('email')->addError($emailError);
            }
        }

        return $this->render('security/resetpsw_email.html.twig', [
            'emailform' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/reset/{id}", name="app_changePsw")
     */
    public function resetPasswordChangePassword(Request $request, $id = "", UserPasswordEncoderInterface $passwordEncoder)
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

            return $this->redirectToRoute('index');
        }

        return $this->render('security/change_passwordByReset.html.twig', [
            'changepassform' => $form->createView(),
        ]);
    }
    /**
     * @Route ("/change/{id}", name="app_changePassword")
     */
    public function ChangePassword(Request $request,$id = "", UserPasswordEncoderInterface $passwordEncoder)
    {

        $form = $this->createForm(Change_PasswordFormType::class);
        $form->handleRequest($request);
        $user = $this->getUser();
        $id = $user->getId();
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user, $form->get('NewPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }


        return $this->render('security/change_password.html.twig', [
            'changepass' => $form->createView(),
        ]);

    }
}
