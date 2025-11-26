<?php

namespace App\Service;

use App\Entity\CustomerOrder;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CustomerOrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderManager
{
    public function __construct(private CustomerOrderRepository $orderRepository, private EntityManagerInterface $entityManager) {

    }

    public function getUserBasket(User $user): CustomerOrder {
        return $this->orderRepository->findOneBy(['user' => $user, 'status' => CustomerOrder::STATUS_BASKET]) ?? $this->addUserBasket($user);
    }

    public function addUserBasket(User $user): CustomerOrder {
        $customerOrder = new CustomerOrder();
        $customerOrder->setUser($user);
        $this->entityManager->persist($customerOrder);
        return $customerOrder;
    }

    public function addProduct(CustomerOrder $customerOrder, Product $product, int $quantity = 1): CustomerOrder {
        foreach ($customerOrder->getOrderItems() as $orderItem) {
            if ($product === $orderItem->getProduct()) {
                $orderItem->setQuantity($quantity + $orderItem->getQuantity());
                return $customerOrder;
            }
        }

        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setQuantity($quantity);
        $customerOrder->addOrderItem($orderItem);

        $this->updateOrderPrice($customerOrder);
        return $customerOrder;
    }

    public function clearBasket(CustomerOrder $order): CustomerOrder
    {
        foreach ($order->getOrderItems() as $item) {
            $order->removeOrderItem($item);
        }

        $order->setOrderPrice(0);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    private function updateOrderPrice(CustomerOrder $customerOrder): void
    {
        $total = 0;

        foreach ($customerOrder->getOrderItems() as $item) {
            $total += $item->getQuantity() * $item->getProduct()->getPrice();
        }

        $customerOrder->setOrderPrice($total);
    }

    public function getOrderItem(CustomerOrder $customerOrder, Product $product): ?OrderItem
    {
        foreach ($customerOrder->getOrderItems() as $orderItem) {
            if ($orderItem->getProduct() === $product) {
                return $orderItem;
            }
        }

        return null;
    }

    public function setProductQuantity(CustomerOrder $customerOrder, Product $product, int $quantity): CustomerOrder
    {
        $quantity = max(0, $quantity);

        foreach ($customerOrder->getOrderItems() as $orderItem) {
            if ($orderItem->getProduct() === $product) {
                if ($quantity === 0) {
                    $customerOrder->removeOrderItem($orderItem);
                } else {
                    $orderItem->setQuantity($quantity);
                }

                $this->updateOrderPrice($customerOrder);
                return $customerOrder;
            }
        }
        if ($quantity > 0) {
            return $this->addProduct($customerOrder, $product, $quantity);
        }

        return $customerOrder;
    }
}
