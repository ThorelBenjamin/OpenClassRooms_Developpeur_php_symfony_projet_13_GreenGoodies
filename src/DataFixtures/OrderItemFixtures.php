<?php
namespace App\DataFixtures;

use App\Entity\CustomerOrder;
use App\Entity\OrderItem;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrderItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
            CostumerOrderFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        for ($o = 0; $o < CostumerOrderFixtures::COUNT; $o++) {

            /** @var CustomerOrder $order */
            $order = $this->getReference(
                CostumerOrderFixtures::REF_PREFIX.$o,
                CustomerOrder::class
            );

            $lineCount = random_int(1, 4);
            $total = 0.0;

            for ($l = 0; $l < $lineCount; $l++) {
                $item = new OrderItem();
                $item->setOrder($order);

                $prodIndex = random_int(0, ProductFixtures::COUNT - 1);

                /** @var Product $product */
                $product = $this->getReference(
                    ProductFixtures::REF_PREFIX.$prodIndex,
                    Product::class
                );

                $qty = random_int(1, 5);
                $item->setQuantity($qty);

                $total += $product->getPrice() * $qty;

                $item->setProduct($product);
                $manager->persist($item);
                $order->addOrderItem($item);
            }

            $order->setOrderPrice($total);
            $manager->persist($order);
        }

        $manager->flush();
    }
}
