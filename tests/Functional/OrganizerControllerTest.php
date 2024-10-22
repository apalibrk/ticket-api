<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Organizer;
use Doctrine\ORM\EntityManagerInterface;

class OrganizerControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    public function testRegisterOrganizer()
    {
        $this->client->request('POST', '/api/organizers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'phone' => '1234567890'
        ]));

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $organizer = $this->entityManager->getRepository(Organizer::class)->findOneBy(['email' => 'john.doe@example.com']);
        $this->assertNotNull($organizer);
        $this->assertEquals('John Doe', $organizer->getName());
    }

    public function testLoginOrganizer()
    {
        // First, register the organizer
        $this->client->request('POST', '/api/organizers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'John Doe',
            'email' => 'john.doe2@example.com',
            'password' => 'password',
            'phone' => '123-456-7890'
        ]));

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        // Then, attempt to login
        $this->client->request('POST', '/api/organizers/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'john.doe2@example.com',
            'password' => 'password'
        ]));

        $this->assertEquals(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $responseContent);
    }
}