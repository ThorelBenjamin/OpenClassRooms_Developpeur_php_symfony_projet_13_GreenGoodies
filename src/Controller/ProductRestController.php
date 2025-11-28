<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class ProductRestController extends AbstractController
{
    #[Route('/api/products', name: 'products', methods: ['GET'])]
    public function getAllProduct(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Authentification requise'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->isApiActivate()) {
            return new JsonResponse(['message' => 'Accès API non activé'], Response::HTTP_FORBIDDEN);
        }

        $productList = $productRepository->findAll();

        $jsonProductList = $serializer->serialize($productList, 'json', ['groups' => 'getProducts']);
        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }
}
