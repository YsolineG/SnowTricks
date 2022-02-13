<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot/password', name: 'forgot_password')]
    public function index(Request $request, UserRepository $userRepository, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $user = $userRepository->findOneBy(['username' => $data]);
            if(!$user){
                // On envoie un message flash
                $this->addFlash('danger', 'Ce pseudo n\'existe pas');
                return $this->redirectToRoute('login');
            }

            $token = $tokenGenerator->generateToken();
//            $token = $user->setResetToken(md5(uniqid()));

            try {
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Une erreur est survenue : '. $e->getMessage());
                return $this->redirectToRoute('login');
            }

            // On génère l'URL de réinitialisation de mot de passe
            $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $emailContent = <<<HTML
<p>Bonjour,</p>
<p>Une demande de réinitialisation de mot de passe a été effectuée pour le site Snow-Tricks.</p>
<p>Veuillez cliquer sur le lien suivant : <a href="${url}">${url}</a></p>
HTML;

            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom('no-reply@swon-tricks.com')
                ->setTo($user->getEmail())
                ->setBody($emailContent, 'text/html');
            $mailer->send($message);

            $this->addFlash('message', 'Un email de réinitialisation de mot de passe vous a été envoyé');

            return $this->redirectToRoute('login');
        }

        return $this->render('forgot_password/index.html.twig', [
            'controller_name' => 'ForgotPasswordController',
            'usernameForm' => $form->createView(),
        ]);
    }

    #[Route('/reset/password/{token}', name: 'reset_password')]
    public function resetPassword($token, Request $request, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);
        if(!$user) {
            $this->addFlash('danger', 'Token inconnu');
            return $this->redirectToRoute('login');
        }
        if($request->isMethod('POST')) {
            $user->setResetToken(null);

            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $request->request->get('password')
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Mot de passe modifié avec succès');
            return $this->redirectToRoute('login');
        } else {
            return $this->render('forgot_password\reset_password.html.twig', [
                'token' => $token
            ]);
        }
    }
}
