<?php

namespace App\DataFixtures;

use App\Entity\Images;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ImagesFixtures extends Fixture implements DependentFixtureInterface //DependentFixture permet de changer l ordre car fixture alpha et Image av Product sauf que dans image il ya get ref qui vient de product voir lg 29
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($img = 1; $img <= 20; $img++) {
            $image = new Images();
            $image->setName($faker->image(null, 640, 480));
            $product = $this->getReference('prod-' . rand(1, 10));
            $image->setProducts($product);
            $manager->persist($image);
        }
        $manager->flush();
    }
    //on passe un tableau des dependances avant Image ici Product
    public function getDependencies(): array
    {
        return [
            ProductsFixtures::class
        ];
    }
}
