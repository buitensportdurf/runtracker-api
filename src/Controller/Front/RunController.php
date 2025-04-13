<?php

namespace App\Controller\Front;

use App\Entity\Run;
use App\Repository\RunRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/run')]
class RunController extends AbstractController
{
    public function __construct(
        private readonly RunRepository $repo,
    ) {}

    #[Route(path: '/show/{id}')]
    public function show(Run $run): Response
    {
        return $this->render('run/show.html.twig', [
            'run' => $run,
        ]);
    }

    #[Route(path: '/index/{year}')]
    public function index(int $year): Response
    {
        return $this->render('run/index.html.twig', [
            'year' => $year,
            'runs' => $this->repo->findAllByYear($year),
        ]);
    }
}