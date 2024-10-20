<?php

namespace App\Controller;

use App\DTO\OrganizerDTO;
use App\Entity\Organizer;
use App\Service\OrganizerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/api/organizers')]
#[OA\Tag(name: 'Organizers')]
class OrganizerController extends AbstractController
{
    private OrganizerService $organizerService;

    public function __construct(OrganizerService $organizerService)
    {
        $this->organizerService = $organizerService;
    }

    #[OA\Post(
        summary: 'Create a new organizer.',
        requestBody: new OA\RequestBody(
            description: 'Organizer data',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe', description: 'Name of the organizer'),
                    new OA\Property(property: 'email', type: 'string', example: 'john.doe@example.com', description: 'Email of the organizer'),
                    new OA\Property(property: 'phone', type: 'string', example: '+1234567890', description: 'Phone number of the organizer'),
                    new OA\Property(property: 'password', type: 'string', example: 'password', description: 'Password of the organizer')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Organizer created successfully',
                content: new OA\JsonContent(ref: new Model(type: Organizer::class, groups: ['full']))
            ),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 500, description: 'Internal server error')
        ],
        security: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                type: 'http',
                scheme: 'bearer'
            )
        ]
    )]
    #[Route('', methods: ['POST'])]
    public function createOrganizer(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $organizerDTO = new OrganizerDTO();
        $organizerDTO->setName($data['name']);
        $organizerDTO->setEmail($data['email']);
        $organizerDTO->setPhone($data['phone']);
        $organizerDTO->setPassword($data['password']); // Consider hashing the password

        $organizer = $this->organizerService->createOrganizer($organizerDTO);
        return $this->json($organizer, JsonResponse::HTTP_CREATED);
    }

    #[OA\Put(
        summary: 'Update an existing organizer.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
                description: 'The ID of the organizer to update'
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Updated organizer data',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'John Doe', description: 'Name of the organizer'),
                    new OA\Property(property: 'email', type: 'string', example: 'john.doe@example.com', description: 'Email of the organizer'),
                    new OA\Property(property: 'phone', type: 'string', example: '+1234567890', description: 'Phone number of the organizer'),
                    new OA\Property(property: 'password', type: 'string', example: 'password', description: 'Password of the organizer')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Organizer updated successfully',
                content: new OA\JsonContent(ref: new Model(type: Organizer::class, groups: ['full']))
            ),
            new OA\Response(response: 400, description: 'Invalid input'),
            new OA\Response(response: 404, description: 'Organizer not found')
        ],
        security: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                type: 'http',
                scheme: 'bearer'
            )
        ]
    )]
    #[Route('/{id}', methods: ['PUT'])]
    #[ParamConverter('organizer', class: Organizer::class)]
    public function updateOrganizer(Request $request, Organizer $organizer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $organizerDTO = new OrganizerDTO();
        $organizerDTO->setName($data['name']);
        $organizerDTO->setEmail($data['email']);
        $organizerDTO->setPhone($data['phone']);
        $organizerDTO->setPassword($data['password']); // Consider hashing the password

        $updatedOrganizer = $this->organizerService->updateOrganizer($organizer, $organizerDTO);
        return $this->json($updatedOrganizer);
    }

    #[OA\Delete(
        summary: 'Delete an organizer.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
                description: 'The ID of the organizer to delete'
            )
        ],
        responses: [
            new OA\Response(response: 204, description: 'Organizer deleted successfully'),
            new OA\Response(response: 404, description: 'Organizer not found')
        ],
        security: [
            new OA\SecurityScheme(
                securityScheme: 'bearerAuth',
                type: 'http',
                scheme: 'bearer'
            )
        ]
    )]
    #[Route('/{id}', methods: ['DELETE'])]
    #[ParamConverter('organizer', class: Organizer::class)]
    public function deleteOrganizer(Organizer $organizer): JsonResponse
    {
        $this->organizerService->deleteOrganizer($organizer);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[OA\Get(
        summary: 'List all organizers.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns a list of all organizers',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: Organizer::class, groups: ['full'])))
            )
        ]
    )]
    #[Route('', methods: ['GET'])]
    public function listOrganizers(): JsonResponse
    {
        $organizers = $this->organizerService->findAll();
        return $this->json($organizers);
    }
}