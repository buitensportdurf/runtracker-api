<?php

namespace App\Controller\Api;

use App\Entity\Run;
use App\Repository\RunRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractFOSRestController
{
    #[Rest\Get(path: '/api/runs')]
    #[Rest\View(serializerGroups: ['from_run'])]
    #[OA\Parameter(
        name: 'year',
        description: 'Only get a specific year',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'Get single page',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'pageSize',
        description: 'Set page size, default is 100',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns all runs',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'total', type: 'int'),
                new OA\Property(property: 'pages', type: 'int'),
                new OA\Property(property: 'run', type: 'array', items: new OA\Items(ref: new Model(type: Run::class))),
            ],
            type: 'object'
        )
    )]
    public function getRunsAction(
        RunRepository $repository,
        Request       $request,
    ): array
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

    #[Rest\Get(path: '/api/runs/{id}')]
    #[Rest\View(serializerGroups: ['from_run'])]
    #[OA\Response(
        response: 200,
        description: 'Returns one run',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'run', ref: new Model(type: Run::class)),
            ],
            type: 'object'
        )
    )]
    public function getRunAction(Run $run): array
    {
        return ['run' => $run];
    }
}