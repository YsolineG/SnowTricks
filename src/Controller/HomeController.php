<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * Home page
     *
     * @Route("/home", name="home")
     */
    public function index(FigureRepository $figureRepository, Request $request): Response
    {
        // On définie le nombre d'éléments par page
        $limit = 2;
        // On récupère le numéro de page
        $page = $request->query->get('page', 1);
        // On récupère les figures de la page
        $figures = $figureRepository->getPaginatedFigures($page, $limit);

        return $this->render('home/index.html.twig', [
            'figures' => $figures
        ]);
    }

    #[Route('/api/figures', name: 'api_figures')]
    public function getFigures(FigureRepository $figureRepository, Request $request): JsonResponse
    {
        // On définie le nombre d'éléments par page
        $limit = 2;
        // On récupère le numéro de page
        $page = $request->query->get('page', 1);
        // On récupère les figures de la page
        $figures = $figureRepository->getPaginatedFigures($page, $limit);

        return $this->json($figures);
    }
}
