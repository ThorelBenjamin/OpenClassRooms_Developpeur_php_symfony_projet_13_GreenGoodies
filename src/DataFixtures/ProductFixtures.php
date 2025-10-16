<?php
namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public const REF_PREFIX = 'product_';
    public const COUNT = 10;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < self::COUNT; $i++) {
            $p = new Product();
            $p->setName($faker->words(3, true));
            $p->setShortDescription($faker->sentence(8));
            $p->setFullDescription($faker->paragraphs(3, true));
            $p->setPrice($faker->randomFloat(2, 5, 200));
            $p->setPicture($faker->imageUrl(640, 480, 'technics', true));
            $manager->persist($p);

            $this->addReference(self::REF_PREFIX.$i, $p);
        }

        $manager->flush();
    }
}
