<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddFigureController extends AbstractController
{
    #[Route('/add/figure', name: 'add_figure')]
    public function index(): Response
    {
        return $this->render('add_figure/index.html.twig', [
            'controller_name' => 'AddFigureController',
        ]);
    }
}
