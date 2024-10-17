<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Organizer;

class OrganizerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 2; $i++) {
            $organizer = new Organizer();
            $organizer->setName('Organizer ' . $i);
            $organizer->setEmail('organizer' . $i . '@example.com');
            $organizer->setPhone('123-456-789' . $i);
            $organizer->setPassword('password'); // Consider hashing this

            $manager->persist($organizer);

            // Add reference for use in EventFixtures
            $this->addReference('organizer_' . $i, $organizer);
        }

        $manager->flush();
    }
}
