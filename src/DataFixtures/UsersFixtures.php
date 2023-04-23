<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class UsersFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Users();
        $admin->setEmail('admin@demo.fr');
        $admin->setLastname('Chambon');
        $admin->setFirstname('Anthony');
        $admin->setAdress('39 chemin de Crillon');
        $admin->setZipcode('84330');
        //$admin->setResetToken("kyhgkhfk557");
        $admin->setCity('Caromb');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'azerty')
        );
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $faker = Faker\Factory::create('fr_FR');

        for ($usr = 1; $usr <= 6; $usr++) {
            $user = new Users();
            $user->setEmail($faker->email);
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            // $user->setResetToken("kyhgkhfk557");
            $user->setAdress($faker->streetAddress);
            $user->setZipcode(str_replace(' ', '', $faker->postcode)); //genere des code5caract plus espace dc trop lg passe methode pour supp espace
            $user->setCity($faker->city);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'secret')
            );
            $manager->persist($user);
            $manager->flush();
        }
    }
}
