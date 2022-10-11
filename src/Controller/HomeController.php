<?php


namespace App\Controller;


use App\Entity\Run;
use App\Service\RunParserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/')]
    public function index()
    {
        return $this->redirectToRoute('app.swagger_ui');
    }

    #[Route(path: '/parse')]
    public function parse(RunParserService $rp)
    {
        $rp->updateRuns();
        return $this->render('base.html.twig');
    }

    /**
     * Retrieves one race
     */
    #[Route(path: '/race/{id}')]
    public function getRaceAction(Run $race)
    {
        return $this->json($race);
    }
}