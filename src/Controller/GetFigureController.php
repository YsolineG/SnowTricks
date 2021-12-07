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

class GetFigureController extends AbstractController
{
    #[Route('/get/figure/{id}', name: 'get_figure')]
    public function index(int $id, FigureRepository $figureRepository, Request $request)
    {
//        $figure = $this->getDoctrine()
//            ->getRepository(Figure::class)
//            ->find($id);
        $figure = $figureRepository->find($id);

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

        return $this->render('get_figure/index.html.twig', [
            'controller_name' => 'GetFigureController',
            'figure' => $figure,
            'commentForm' => $form->createView(),
        ]);
    }
}
