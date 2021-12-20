<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Photos;
use App\Entity\Video;
use App\Form\FigureFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddFigureController extends AbstractController
{
    #[Route('/add/figure', name: 'add_figure')]
    public function index(Request $request): Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureFormType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
                $photoEntity = new Photos();
                $photoEntity->setName($file);
                $figure->addPhoto($photoEntity);
            }

            $videosUrl = $form->get('videos')->getData();

            foreach ($videosUrl as $url){
                $videoEntity = new Video();
                $url = str_replace('watch?v=', 'embed/', $url);
                $videoEntity->setUrl($url);
                $figure->addVideo($videoEntity);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($figure);
            $entityManager->flush();
            $request->getSession()->getFlashBag()->add('success', 'La figure a bien été ajoutée');
            return $this->redirectToRoute('home');
        }

        return $this->render('add_figure/index.html.twig', [
            'figureForm' => $form->createView(),
        ]);
    }

    /**
     *  @Route("/delete/{id}", name="delete")
     */
    public function delete(int $id, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $figure = $entityManager->getRepository(Figure::class)->find($id);
        $photos = $figure->getPhoto();
        if($photos){
            foreach ($photos as $photo){
                $name = $this->getParameter('photo_directory') . '/' . $photo->getName();
                if(file_exists($name)) {
                    unlink($name);
                }
            }
        }
        $entityManager->remove($figure);
        $entityManager->flush();
        $request->getSession()->getFlashBag()->add('success', 'La figure a bien été supprimée');

        return $this->redirectToRoute('home');
    }
}
