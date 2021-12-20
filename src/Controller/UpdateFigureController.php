<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Photos;
use App\Entity\Video;
use App\Form\FigureFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateFigureController extends AbstractController
{
    #[Route('/update/figure/{id}', name: 'update_figure')]
    public function index(Request $request, Figure $figure): Response
    {
        $form = $this->createForm(FigureFormType::class, $figure);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // On récupère les photos transmises
            $photos = $form->get('photos')->getData();

            // On boucle sur les photos
            foreach ($photos as $photo) {
                // On génère un nouveau nom de fichier
                $file = md5(uniqid()) . '.' . $photo->guessExtension();

                // On copie le fichier dans le dossier uploads
                $photo->move(
                    $this->getParameter('photo_directory'),
                    $file
                );

                // On stocke la photo dans la bdd (son nom)
                $pht = new Photos();
                $pht->setName($file);
                $figure->addPhoto($pht);
            }

            $videosUrl = $form->get('videos')->getData();

            foreach ($videosUrl as $url) {
                $videoEntity = new Video();
                $url = str_replace('watch?v=', 'embed/', $url);
                $videoEntity->setUrl($url);
                $figure->addVideo($videoEntity);
            }

            $this->getDoctrine()->getManager()->flush();
            $request->getSession()->getFlashBag()->add('success', 'La figure a bien été modifiée');
            return $this->redirectToRoute('home');
        }

        return $this->render('update_figure/index.html.twig', [
            'figureForm' => $form->createView(),
            'figure' => $figure
        ]);
    }

    #[Route('/delete/photo/{id}', name: 'delete_photo', methods: 'DELETE')]
    public function deletePhoto(Photos $photo, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$photo->getId(), $data['_token'])) {
            // Onrécupère le nom de l'image
            $name = $photo->getName();
            // On supprime le fichier
            unlink($this->getParameter('photo_directory') . '/' . $name);

            // On supprime l'entrée de la bdd
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($photo);
            $entityManager->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
    }

    #[Route('/delete/video/{id}', name: 'delete_video', methods: 'DELETE')]
    public function deleteVideo(Video $video, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        // On vérifie si le token est valide

        if($this->isCsrfTokenValid('delete'.$video->getId(), $data['_token'])) {
            $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($video);
        $entityManager->flush();

        return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        } 
    }
}
