<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     * @param GameRepository $gameRepository
     * @return Response
     */
    public function index(GameRepository $gameRepository)
    {
        $games = $gameRepository->findAll();

        return $this->render('dashboard/index.html.twig', [
            'games' => $games,
        ]);
    }
}
