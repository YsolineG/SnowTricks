<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, \Swift_Mailer $mailer): Response
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
            $message = (new \Swift_Message('Activation de votre compte'))
                // On attribue l'expéditeur
                ->setFrom('no-reply@swon-tricks.com')
                // On attribue le destinataire
                ->setTo($user->getEmail())
                // Oncrée le contenu
                ->setBody(
                    $this->renderView('emails/activation.html.twig', ['token' => $user->getActiviationToken()]),
                    'text/html'
                )
            ;

            // On envoie l'email
            $mailer->send($message);

            // generate a signed url and email it to the user
//            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
//                (new TemplatedEmail())
//                    ->from(new Address('ysoline.ganster@gmail.com', 'SnowTricks Mail Bot'))
//                    ->to($user->getEmail())
//                    ->subject('Please Confirm your Email')
//                    ->htmlTemplate('registration/confirmation_email.html.twig')
//            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }

    #[Route('/activation/{token}', name: 'activation')]
    public function activation($token, UserRepository $userRepository, Request $request)
    {
        // On vérifie si un utilisateur a ce token
        $user = $userRepository->findOneBy(['activiation_token' => $token]);

        if(!$user){
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // On supprime le token
        $user->setActiviationToken(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // On envoie un message flash
//        $this->addFlash('success', 'Votre compte est activé');
        $request->getSession()->getFlashBag()->add('success', 'Votre compte est activé');
        return $this->redirectToRoute('home');
    }
}
