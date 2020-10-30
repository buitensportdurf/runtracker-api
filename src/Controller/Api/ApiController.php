<?php


namespace App\Controller\Api;


use App\Collections\PaginatedCollection;
use App\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractFOSRestController
{
    /**
     * Retrieves all races
     * @Rest\Get(path="/races")
     */
    public function getRacesAction(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request)
    {
        $page = $request->query->getInt('page', 1);
        $pageSize = $request->query->getInt('page_size', 10);
        $repo = $em->getRepository(Race::class);
        $query = $repo->createQueryBuilder('r')->getQuery();

        $races = $paginator->paginate($query, $page, $pageSize);

        return $this->handleView($this->view(new PaginatedCollection($races), 200));
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