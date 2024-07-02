<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\RegistrationFormType;
use App\Form\ReInitialisePasswordType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use function PHPUnit\Framework\throwException;

class SecurityController extends AbstractController
{

    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = $userRepository->findOneBy(['email' => $lastUsername]);


        if ($this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_accueil'));
        }



        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/mot_de_passe_oublie', name: 'app_forgot_password')]
    public function sendEmailResetPassword(Request $request, UserRepository $userRepository, EntityManagerInterface $em, MailerService $mailerService): Response
    {

        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();

            $user = $userRepository->findOneBy(['name' => $name]);

            if ($user) {

                $email = $user->getEmail();
                $token = bin2hex(random_bytes(32));
                $user->setTokenVerified($token);

                $em->flush();

                $mailerService->SendEmailResetPassword($email, $token, $name);
                $this->addFlash('success', 'Un mail de reinitialisation de votre mot de passe  a été envoyé dans votre boite Email');
                return $this->redirectToRoute('app_login');
            } else {
                throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
            }
        }
        return   $this->render(
            'security/mot_de_passe_oublie.html.twig',
            [
                'form' => $form
            ]
        );
    }

    #[Route(path: '/nouveau_mot_de_passe/{token}', name: 'app_new_password')]
    public function resetPassword(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        string $token,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {

        $form = $this->createForm(ReInitialisePasswordType::class);
        $form->handleRequest($request);

        $user = $userRepository->findOneBy(['tokenVerified' => $token]);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('plainPassword')->getData();

            if ($user) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $password
                    )
                );
            }

            $newToken = '';
            $user->setTokenVerified($newToken);

            $em->flush();

            $this->addFlash('success', 'Votre mot de passe a été  changé avec succès ');
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/new_password.html.twig',
            [
                'form' => $form,
            ]
        );
    }
}
