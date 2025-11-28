<?php

namespace App\Controller;

use App\Entity\CustomerOrder;
use App\Repository\CustomerOrderRepository;
use App\Repository\OrderItemRepository;
use App\Service\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(CustomerOrderRepository $orderRepository): Response
    {
        $user = $this->getUser();

        $orders = $orderRepository->findBy(
            [
                'user' => $user,
                'status' => CustomerOrder::STATUS_PAID
            ],
            ['date' => 'DESC']
        );

        return $this->render('user/dashboard.html.twig', [
            'controller_name' => 'UserController', 'orders' => $orders
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/basket', name: 'app_basket')]
    public function basket(OrderManager $orderManager): Response
    {
        $user = $this->getUser();

        return $this->render('user/basket.html.twig', [
            'controller_name' => 'UserController', 'order' => $orderManager->getUserBasket($user)
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/basket/clear', name: 'app_basket_clear', methods: ['GET'])]
    public function clearBasket(OrderManager $orderManager): Response
    {
        $user = $this->getUser();
        $basket = $orderManager->getUserBasket($user);

        $orderManager->clearBasket($basket);

        return $this->redirectToRoute('app_basket');
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/dashboard/toggle-api', name: 'app_toggle_api', methods: ['POST'])]
    public function toggleApi(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('toggle_api', $submittedToken)) {
            $this->addFlash('error', 'Jeton invalide.');
            return $this->redirectToRoute('app_dashboard');
        }

        $newState = ! $user->isApiActivate();
        $user->setApiActivate($newState);

        $em->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/basket/validate', name: 'app_basket_validate', methods: ['POST'])]
    public function validateBasket(OrderManager $orderManager, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $order = $orderManager->getUserBasket($user);

        if ($order->getOrderItems()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_basket');
        }

        $order->setStatus(CustomerOrder::STATUS_PAID);
        $order->setDate(new \DateTime());

        $em->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/dashboard/delete-account', name: 'app_delete_account', methods: ['POST'])]
    public function deleteAccount(Request $request, CustomerOrderRepository $orderRepo, OrderItemRepository $itemRepo, EntityManagerInterface $em): RedirectResponse {
        $user = $this->getUser();

        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_account', $submittedToken)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_dashboard');
        }

        $orders = $orderRepo->findBy(['user' => $user]);

        foreach ($orders as $order) {
            foreach ($order->getOrderItems() as $item) {
                $em->remove($item);
            }
            $em->remove($order);
        }
        $this->container->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Votre compte a bien été supprimé.');

        return $this->redirectToRoute('app_home');
    }
}

