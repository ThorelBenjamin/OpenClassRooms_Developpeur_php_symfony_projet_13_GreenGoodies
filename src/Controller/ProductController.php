<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

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
    public function show(int $id, Request $request, OrderManager $orderManager, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();
        $productInBasket = false;
        $quantityInBasket = 1;

        if ($user) {
            $customerOrder = $orderManager->getUserBasket($user);
            $orderItem = $orderManager->getOrderItem($customerOrder, $product);
            $productInBasket = $orderItem !== null;
            $quantityInBasket = $orderItem ? $orderItem->getQuantity() : 1;
        }

        if ($request->isMethod('POST')) {
            $submittedToken = $request->getPayload()->get('_csrf_token');
            $quantity = max(0, $request->request->getInt('quantity', 1));

            if ($this->isCsrfTokenValid('basket', $submittedToken)) {
                $customerOrder = $orderManager->getUserBasket($user);
                if ($productInBasket) {
                    $orderManager->setProductQuantity($customerOrder, $product, $quantity);
                } else{
                    $orderManager->addProduct($customerOrder, $product, $quantity);
                }

                $entityManager->flush();
                return $this->redirectToRoute('app_basket');
            }
        }

        return $this->render('product/product.html.twig', [
            'controller_name' => 'HomeController', 'product' => $product, 'productBasket' => $productInBasket, 'quantityInBasket' => $quantityInBasket
        ]);
    }

}

