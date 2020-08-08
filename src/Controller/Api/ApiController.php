<?php


namespace App\Controller\Api;


use App\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ApiController extends AbstractFOSRestController
{
    /**
     * Retrieves all races
     * @Rest\Get(path="/races")
     */
    public function getRacesAction(EntityManagerInterface $em)
    {
        $races = $em->getRepository(Race::class)->findAll();
        return $this->handleView($this->view($races, 200));
    }

    /**
     * Retrieves one race
     * @Rest\Get(path="/race/{id}")
     */
    public function getRaceAction(Race $race)
    {
        return $this->handleView($this->view($race, 200));
    }
}