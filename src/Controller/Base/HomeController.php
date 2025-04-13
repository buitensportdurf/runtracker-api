<?php

namespace App\Controller\Base;

use App\Entity\Run;
use App\Service\RunParserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'home')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_front_organization_index');
//        return $this->redirectToRoute('app.swagger_ui');
    }

    #[Route(path: '/parse')]
    public function parse(RunParserService $rp): Response
    {
        $rp->updateRuns();
        return $this->render('base/base.html.twig');
    }

    #[Route(path: '/empty')]
    public function empty(): Response
    {
        return $this->render('base/one.column.html.twig');
    }

    #[Route(path: '/race/{id}')]
    public function getRaceAction(Run $race): Response
    {
        return $this->json($race, context: [
            'groups' => ['from_run'],
        ]);
    }
}