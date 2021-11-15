<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            $this->getDoctrine()->getManager()->flush();
            $request->getSession()->getFlashBag()->add('success', 'La figure a bien été modifiée');
            return $this->redirectToRoute('home');
        }

        return $this->render('update_figure/index.html.twig', [
            'figureForm' => $form->createView(),
        ]);
    }
}
