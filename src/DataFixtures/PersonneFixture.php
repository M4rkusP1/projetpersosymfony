<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use App\Entity\Personne;


class PersonneFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        // $product = new Product();
        // $manager->persist($product);
        for ($i=0; $i < 80; $i ++) {
            $personne = new Personne();
            $personne->setFirstname($faker->firstName);
            $personne->setName($faker->lastName);
            $personne->setAge($faker->numberBetween(2, 99));
            $manager->persist($personne);
        } 
        $manager->flush();
    }
}
