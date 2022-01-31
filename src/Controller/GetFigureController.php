<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetFigureController extends AbstractController
{
    #[Route('/get/figure/{id}-{slug}', name: 'get_figure')]
    public function index(Figure $figure, Request $request, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable());

            /** @var User $user */
            $user = $this->getUser();
            $comment->setUser($user);
            $comment->setFigure($figure);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $request->getSession()->getFlashBag()->add('success', 'Votre commentaire a bien été ajoutée');
        }

        $limit = 2;
        $page = $request->query->get('page', 1);
        $comments = $commentRepository->getPaginatedCommentsByFigureId($figure->getId(), $page, $limit);

        return $this->render('get_figure/index.html.twig', [
            'controller_name' => 'GetFigureController',
            'figure' => $figure,
            'commentForm' => $form->createView(),
            'comments' => $comments
        ]);
    }

    #[Route('/api/figure/{id}/comments', name: 'api_figure_comments')]
    public function getComments(Figure $figure, CommentRepository $commentRepository, Request $request): JsonResponse
    {
        // On définie le nombre d'éléments par page
        $limit = 2;
        // On récupère le numéro de page
        $page = $request->query->get('page', 1);
        // On récupère les figures de la page
        $comments = $commentRepository->getPaginatedCommentsByFigureId($figure->getId(), $page, $limit);

        return $this->json($comments);
    }
}
