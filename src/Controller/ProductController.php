<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/dashboard.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/product/{id}', name: 'product.show', requirements: ['id' => '\d+'])]
    public function show(Product $product): Response
    {
        return $this->render('product/product.html.twig', [
            'controller_name' => 'HomeController', 'product' => $product
        ]);
    }

}
