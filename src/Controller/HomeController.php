<?php

namespace App\Controller;

use App\Entity\Figure;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Home page
     *
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        $figures = $this->getDoctrine()
            ->getRepository(Figure::class)
            ->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'figures' => $figures
        ]);
    }
}
