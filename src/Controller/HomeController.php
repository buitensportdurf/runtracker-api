<?php


namespace App\Controller;


use App\Service\RunParserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(RunParserService $rp)
    {
        dump($rp->updateRuns());
        return $this->render('base.html.twig');
    }
}