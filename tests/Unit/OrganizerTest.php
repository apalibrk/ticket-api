<?php
namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Service\OrganizerService;
use App\DTO\OrganizerDTO;
use App\Entity\Organizer;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class OrganizerServiceTest extends TestCase
{
    private $entityManager;
    private $organizerService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->organizerService = new OrganizerService($this->entityManager);
    }

    public function testCreateOrganizerWithEmptyName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Organizer name cannot be empty');

        $organizerDTO = $this->createMock(OrganizerDTO::class);
        $organizerDTO->method('getName')->willReturn('');
        $organizerDTO->method('getEmail')->willReturn('valid@example.com');
        $organizerDTO->method('getPhone')->willReturn('123-456-7890');
        $organizerDTO->method('getPassword')->willReturn('password');

        $this->organizerService->createOrganizer($organizerDTO);
    }

    public function testCreateOrganizerWithInvalidEmail()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        $organizerDTO = $this->createMock(OrganizerDTO::class);
        $organizerDTO->method('getName')->willReturn('Organizer Name');
        $organizerDTO->method('getEmail')->willReturn('invalid-email');
        $organizerDTO->method('getPhone')->willReturn('123-456-7890');
        $organizerDTO->method('getPassword')->willReturn('password');

        $this->organizerService->createOrganizer($organizerDTO);
    }

    public function testCreateOrganizerWithInvalidPhone()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid phone number format');

        $organizerDTO = $this->createMock(OrganizerDTO::class);
        $organizerDTO->method('getName')->willReturn('Organizer Name');
        $organizerDTO->method('getEmail')->willReturn('valid@example.com');
        $organizerDTO->method('getPhone')->willReturn('invalid-phone');
        $organizerDTO->method('getPassword')->willReturn('password');

        $this->organizerService->createOrganizer($organizerDTO);
    }

    public function testCreateOrganizerWithShortPassword()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password must be at least 6 characters long');

        $organizerDTO = $this->createMock(OrganizerDTO::class);
        $organizerDTO->method('getName')->willReturn('Organizer Name');
        $organizerDTO->method('getEmail')->willReturn('valid@example.com');
        $organizerDTO->method('getPhone')->willReturn('123-456-7890');
        $organizerDTO->method('getPassword')->willReturn('12345');

        $this->organizerService->createOrganizer($organizerDTO);
    }

    public function testCreateOrganizerSuccessfully()
    {
        $organizerDTO = $this->createMock(OrganizerDTO::class);
        $organizerDTO->method('getName')->willReturn('Organizer Name');
        $organizerDTO->method('getEmail')->willReturn('valid@example.com');
        $organizerDTO->method('getPhone')->willReturn('123-456-7890');
        $organizerDTO->method('getPassword')->willReturn('password');

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Organizer::class));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $organizer = $this->organizerService->createOrganizer($organizerDTO);

        $this->assertInstanceOf(Organizer::class, $organizer);
        $this->assertEquals('Organizer Name', $organizer->getName());
        $this->assertEquals('valid@example.com', $organizer->getEmail());
        $this->assertEquals('123-456-7890', $organizer->getPhone());
        $this->assertEquals('password', $organizer->getPassword());
    }
}