<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Event;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $event = new Event();
            $event->setTitle('Event ' . $i);
            $event->setDate(new \DateTime());
            $event->setVenue('Venue ' . $i);
            $event->setCapacity(100 + $i * 10);

            // Reference to the organizer
            $organizer = $this->getReference('organizer_' . ($i % 2)); // Use 2 organizers
            $event->setOrganizer($organizer);

            $manager->persist($event);

            // Add reference for use in TicketFixtures
            $this->addReference('event_' . $i, $event);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            OrganizerFixtures::class, // Ensure this runs after OrganizerFixtures
        ];
    }
}

