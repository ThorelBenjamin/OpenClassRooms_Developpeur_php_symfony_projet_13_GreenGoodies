<?php

namespace App\Controller;

use App\Entity\CustomerOrder;
use App\Repository\CustomerOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(CustomerOrderRepository $orderRepository): Response
    {
        $user = $this->getUser();

        $orders = $orderRepository->findBy(
            ['user' => $user],
            ['date' => 'DESC']
        );

        return $this->render('user/dashboard.html.twig', [
            'controller_name' => 'UserController', 'orders' => $orders
        ]);
    }
}
