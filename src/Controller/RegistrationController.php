<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        Swift_Mailer $mailer
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // On génère le token d'activiation
            $user->setActiviationToken(md5(uniqid()));

            // On récupère la photo transmise
            $photo = $form->get('photo')->getData();
            // On génère un nouveau nom de fichier
            $file = md5(uniqid()) . '.' . $photo->guessExtension();
            // On copie le fichier dans le dossier user de uploads
            $photo->move(
                $this->getParameter('user_photo'),
                $file
            );
            $user->setPhoto($file);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // On crée le message
            $message = (new Swift_Message('Activation de votre compte'))
                // On attribue l'expéditeur
                ->setFrom('no-reply@snow-tricks.com')
                // On attribue le destinataire
                ->setTo($user->getEmail())
                // Oncrée le contenu
                ->setBody(
                    $this->renderView('emails/activation.html.twig', ['token' => $user->getActiviationToken()]),
                    'text/html'
                );

            // On envoie l'email
            $mailer->send($message);

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/activation/{token}', name: 'activation')]
    public function activation($token, UserRepository $userRepository, Request $request): RedirectResponse
    {
        // On vérifie si un utilisateur a ce token
        $user = $userRepository->findOneBy(['activiation_token' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // On supprime le token
        $user->setActiviationToken(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // On envoie un message flash
        $request->getSession()->getFlashBag()->add('success', 'Votre compte est activé');
        return $this->redirectToRoute('home');
    }
}
