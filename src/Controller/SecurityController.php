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
use App\Entity\SecurityCode;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

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
            'error' => $error
        ]);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    { }

    /**
     * @Route ("/reset", name="app_reset")
     */
    public function resetPasswordGetEmail(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(EmailFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $email = $form->get('email')->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user != null) {
                $secCode = new SecurityCode();
                $secCode->setSecureCode();
                $secCode->setPurpose(SecurityCode::PASSWORD_RESET);
                $secCode->setUser($user);

                $message = (new \Swift_Message('Password Reset'))
                    ->setFrom(['datasuniai@gmail.com' => 'Datašuniai'])
                    ->setTo($email)
                    ->setBody(
                        $this->renderView('security/reset_password.html.twig', [
                            'username' => $user->getUsername(),
                            'code' => $secCode->getCode()
                        ]),
                        'text/html'
                    );
                $mailer->send($message);
                $this->addFlash('success', 'link has been sent to this email: ' . $email);

                $entityManager->persist($secCode);
                $entityManager->flush();
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
     * @Route ("/reset/{code}", name="app_changePsw")
     */
    public function resetPasswordChangePassword(Request $request, $code = "", UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $secCode = $entityManager->getRepository(SecurityCode::class)->findOneBy([
            'purpose' => SecurityCode::PASSWORD_RESET,
            'code' => $code
        ]);

        $user = null;

        $form = $this->createForm(Reset_PasswordFormType::class);
        $form->handleRequest($request);

        if ($secCode != null) {
            $user = $secCode->getUser();

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('newPassword')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->remove($secCode);
                $entityManager->flush();
                $this->addFlash('success', 'Password changed');

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $formAuthenticator,
                    'main'
                );
            }
        }

        if ($user == null) {
            $this->addFlash('danger', 'Sorry, this password reset link is expired or does not exist');

            return $this->render('base.html.twig');
        }

        return $this->render('security/change_passwordByReset.html.twig', [
            'changepassform' => $form->createView()
        ]);
    }

    /**
     * @Route ("/change", name="app_changePassword")
     */
    public function ChangePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(Change_PasswordFormType::class);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($passwordEncoder->isPasswordValid($user, $form->get('CurrentPassword')->getData())) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('NewPassword')->getData()
                    )
                );
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Password changed');

                return $this->redirectToRoute('index');
            } else {
                $this->addFlash('danger', 'Current password is incorrect!');
            }
        }


        return $this->render('security/change_password.html.twig', [
            'changepass' => $form->createView(),
        ]);
    }
}
