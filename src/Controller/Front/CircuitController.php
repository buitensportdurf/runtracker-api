<?php

namespace App\Controller\Front;

use App\Entity\Circuit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/circuit')]
class CircuitController extends AbstractController
{
    #[Route(path: '/show/{id}')]
    public function show(Circuit $circuit): Response
    {
        return $this->render('circuit/show.html.twig', [
            'circuit' => $circuit,
        ]);
    }
}