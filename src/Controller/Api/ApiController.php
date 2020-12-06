<?php


namespace App\Controller\Api;


use App\Entity\Run;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractFOSRestController
{
    /**
     * Retrieves all runs
     * @Rest\Get(path="/runs")
     * @Rest\View(serializerGroups={"from_run"})
     * @OA\Response(
     *     response=200,
     *     description="Returns all runs",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="run", type="array", @OA\Items(ref=@Model(type=Run::class)))
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
     * @Rest\Get(path="/runs/{id}")
     * @Rest\View(serializerGroups={"from_run"})
     * @OA\Response(
     *     response=200,
     *     description="Returns one run",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="run", ref=@Model(type=Run::class) )
     *     )
     * )
     */
    public function getRunAction(Run $run)
    {
        return ['run' => $run];
    }
}