<?php

namespace App\Repository;

use App\Entity\OrderItem;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderItem>
 */
class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }


    /**
     * @return OrderItem[]
     */
    public function findBasketItemsForUser(User $user): array
    {
        return $this->createQueryBuilder('oi')
            ->innerJoin('oi.order', 'o')
            ->innerJoin('oi.product', 'p')
            ->addSelect('o', 'p') // évite le N+1
            ->where('oi.basket = :basket')
            ->andWhere('o.user = :user')
            ->setParameter('basket', true)
            ->setParameter('user', $user)
            ->orderBy('oi.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
