<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Photos;
use App\Entity\User;
use App\Entity\Video;
use App\Form\FigureFormType;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AddFigureController extends AbstractController
{
    #[Route('/add/figure', name: 'add_figure')]
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        if ($user) {
            $figure = new Figure();
            $form = $this->createForm(FigureFormType::class, $figure);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $figure->setCreatedAt(new DateTimeImmutable())
                    ->setSlug(strtolower($slugger->slug($figure->getName())));
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

                foreach ($videosUrl as $url) {
                    if (empty($url) === true) {
                        continue;
                    }

                    $videoEntity = new Video();
                    $url = str_replace('watch?v=', 'embed/', $url);
                    $videoEntity->setUrl($url);
                    $figure->addVideo($videoEntity);
                }

                // Ajouter le user_id
                /** @var User $user */
                $user = $this->getUser();
                $figure->setUser($user);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($figure);
                $entityManager->flush();
                $request->getSession()->getFlashBag()->add('figure-success', 'La figure a bien été ajoutée');
                return $this->redirectToRoute('get_figure', ['id' => $figure->getId(), 'slug' => $figure->getSlug()]);
            }

            return $this->render('add_figure/index.html.twig', [
                'figureForm' => $form->createView(),
            ]);
        }

        return new Response("Vous n'avez pas accès à cette page", 400);

    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(int $id, Request $request): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $figure = $entityManager->getRepository(Figure::class)->find($id);
        $user = $this->getUser();
        $userFigure = $figure->getUser();
        if ($user === $userFigure) {
            $photos = $figure->getPhoto();
            if ($photos) {
                foreach ($photos as $photo) {
                    $name = $this->getParameter('photo_directory') . '/' . $photo->getName();
                    if (file_exists($name)) {
                        unlink($name);
                    }
                }
            }

            $entityManager->remove($figure);
            $entityManager->flush();
            $request->getSession()->getFlashBag()->add('success', 'La figure a bien été supprimée');

            return $this->redirectToRoute('home');
        } else {
            return new Response("Vous n'avez pas accès à cette page", Response::HTTP_UNAUTHORIZED);
        }
    }
}
