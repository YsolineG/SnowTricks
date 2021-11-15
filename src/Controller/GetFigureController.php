<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetFigureController extends AbstractController
{
    #[Route('/get/figure/{id}', name: 'get_figure')]
    public function index(int $id, FigureRepository $figureRepository): Response
    {
//        $figure = $this->getDoctrine()
//            ->getRepository(Figure::class)
//            ->find($id);
        $figure = $figureRepository->find($id);

        return $this->render('get_figure/index.html.twig', [
            'controller_name' => 'GetFigureController',
            'figure' => $figure
        ]);
    }
}
