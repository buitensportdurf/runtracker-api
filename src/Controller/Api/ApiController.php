<?php


namespace App\Controller\Api;


use App\Entity\Run;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractFOSRestController
{
    /**
     * Retrieves all runs
     * @Rest\Get(path="/runs")
     * @Rest\View(serializerGroups={"from_run"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns all runs",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="run", type="array", @SWG\Items(ref=@Model(type=Run::class)))
     *     )
     * )
     */
    public function getRunsAction(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request)
    {
//        $page = $request->query->getInt('page', 1);
//        $pageSize = $request->query->getInt('page_size', 10);
//
//        $repo = $em->getRepository(Run::class);
//        $query = $repo->createQueryBuilder('r')->getQuery();
//
//        $runs = $paginator->paginate($query, $page, $pageSize);
        $runs = $em->getRepository(Run::class)->findAll();

        return ['run' => $runs];
    }

    /**
     * Retrieves one run
     * @Rest\Get(path="/run/{id}")
     * @Rest\View(serializerGroups={"from_run"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns one run",
     *     @SWG\Schema(ref=@Model(type=Run::class))
     * )
     */
    public function getRunAction(Run $run)
    {
        return $run;
    }
}