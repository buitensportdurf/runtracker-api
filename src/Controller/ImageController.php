<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/image')]
class ImageController extends AbstractController
{
    #[Route(path: '/{image}')]
    public function show(Image $image): Response
    {
        return new Response(base64_decode($image->getData()), headers: [
            'Content-Type' => 'image/jpg',
            'Content-Disposition' => 'inline; filename="' . $image->getId() . '.jpg"',
        ]);
    }
}