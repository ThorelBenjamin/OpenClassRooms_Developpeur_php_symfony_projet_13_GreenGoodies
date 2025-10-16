<?php
namespace App\DataFixtures;

use App\Entity\CustomerOrder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CostumerOrderFixtures extends Fixture
{
    public const REF_PREFIX = 'order_';
    public const COUNT = 5;

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < self::COUNT; $i++) {
            $o = new CustomerOrder();
            $o->setDate(new \DateTime('-'.random_int(0, 30).' days'));
            $o->setOrderPrice(0.0); // sera recalculé dans OrderItemFixtures
            $manager->persist($o);

            $this->addReference(self::REF_PREFIX.$i, $o);
        }

        $manager->flush();
    }
}
