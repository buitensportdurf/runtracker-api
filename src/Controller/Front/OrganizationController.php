<?php

namespace App\Controller\Front;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/organization')]
class OrganizationController extends AbstractController
{
    public function __construct(
        private readonly OrganizationRepository $repo,
    ) {}

    #[Route(path: '/index')]
    public function index(): Response
    {
        return $this->render('organization/index.html.twig', [
            'organizations' => $this->repo->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route(path: '/{id}/show')]
    public function show(Organization $organization): Response
    {
        return $this->render('organization/show.html.twig', [
            'organization' => $organization,
        ]);
    }
}