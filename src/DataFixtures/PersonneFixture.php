<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PersonneFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i=0; $i < 100; $i ++) {
            $personne = new Personne();
            $personne->setFirstname('Arth');
            $personne->setName('Athome');
            $personne->setAge('25');
        }
        $manager->flush();
    }
}
