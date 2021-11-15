<?php

namespace App\Controller;

use App\Entity\Figure;
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
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $figure = $entityManager->getRepository(Figure::class)->find($id);
        $entityManager->remove($figure);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
