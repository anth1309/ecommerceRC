<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    private $counter = 1;
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory(name: 'Electrique', parent: null, manager: $manager);
        $this->createCategory('Camions', $parent, manager: $manager);
        $this->createCategory('Chars', $parent, manager: $manager);
        $this->createCategory('voitures', $parent, manager: $manager);

        $parent = $this->createCategory(name: 'Thermique', parent: null, manager: $manager);
        $this->createCategory('Camions', $parent, manager: $manager);
        $this->createCategory('Helicoptere', $parent, manager: $manager);
        $this->createCategory('Avions', $parent, manager: $manager);


        $parent = $this->createCategory(name: 'Electronique', parent: null, manager: $manager);
        $this->createCategory('Radios', $parent, manager: $manager);
        $this->createCategory('Recepteurs', $parent, manager: $manager);
        $this->createCategory('Batteries', $parent, manager: $manager);
        $this->createCategory('Moteurs', $parent, manager: $manager);

        // $parentDeux = new Categories();
        // $parentDeux->setName('Thermique');
        // $parentDeux->setSlug($this->slugger->slug($parentDeux->getName())->lower());
        // $manager->persist($parentDeux);

        // $category = new Categories();
        // $category->setName('Camions');
        // $category->setSlug($this->slugger->slug($category->getName())->lower());
        // $category->setParent($parentDeux);
        // $manager->persist($category);

        $manager->flush();
    }
    public function createCategory(string $name, Categories $parent = null, ObjectManager $manager)
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);
        // Permet de stocker le numero de la category
        $this->addReference('cat-' . $this->counter, $category);
        $this->counter++;
        return $category;
    }
}
