<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Ticket;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $ticket = new Ticket();
            $ticket->setSeatNumber('Seat ' . $i);
            $ticket->setPrice(mt_rand(50, 200));
            $ticket->setStatus('available');

            // Reference to the event created in EventFixtures
            $event = $this->getReference('event_' . ($i % 5)); // cycling through 5 events
            $ticket->setEvent($event);

            $manager->persist($ticket);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            EventFixtures::class, // Ensure this runs after EventFixtures
        ];
    }
}

