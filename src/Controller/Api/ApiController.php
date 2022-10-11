<?php


namespace App\Controller\Api;


use App\Entity\Run;
use App\Repository\RunRepository;
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
     * @OA\Parameter(
     *     name="year",
     *     in="query",
     *     description="Only get a specific year",
     *     required=false,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Get single page",
     *     required=false,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="Set page size, default is 100",
     *     required=false,
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns all runs",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="total", type="int"),
     *         @OA\Property(property="pages", type="int"),
     *         @OA\Property(property="run", type="array", @OA\Items(ref=@Model(type=Run::class))),
     *     )
     * )
     */
    public function getRunsAction(
        RunRepository $repository,
        Request       $request,
    )
    {
        $page = $request->get('page', 1);
        $pageSize = $request->get('pageSize', 100);
        $runs = $repository->findAllByYear($request->get('year'), $pageSize, $pageSize * ($page - 1));

        return [
            'total' => $runs->count(),
            'pages' => ceil($runs->count() / $pageSize),
            'run' => $runs,
        ];
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