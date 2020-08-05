<?php


namespace App\Controller\Api;


use App\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class ApiController extends AbstractFOSRestController
{
    /**
     * @Rest\Route(path="/test")
     */
    public function getRacesAction(EntityManagerInterface $em)
    {
        $races = $em->getRepository(Race::class)->findAll();
        return $this->handleView($this->view($races, 200));
    }
}