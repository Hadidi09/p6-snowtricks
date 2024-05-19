<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $em,
        MailerService $mailerService
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setVerified(false);
            $token = bin2hex(random_bytes(32));
            $user->setTokenVerified($token);

            $em->persist($user);
            $em->flush();

            $mailerService->SendEmailVerification($user->getEmail(), $token, $user->getName());
            $this->addFlash('success', 'Veuillez confirmer votre Email.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verification/email/{token}', name: 'app_verify_email')]
    public function verifyUserEmail(EntityManagerInterface $em, string $token): Response
    {

        $user = $this->userRepository->findOneBy(['tokenVerified' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Ce token n\'est pas valide !');
        }

        $user->setTokenVerified('');
        $user->setVerified(true);

        $em->flush();

        $this->addFlash('success', 'Email validé avec succès ');
        return $this->redirectToRoute('app_login');
    }
}
