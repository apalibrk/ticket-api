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

#[Route('/api/organizers')]
#[OA\Tag(name: "organizers")]
class OrganizerController extends AbstractController
{
    private OrganizerService $organizerService;

    public function __construct(OrganizerService $organizerService)
    {
        $this->organizerService = $organizerService;
    }

    /**
     * Create a new organizer.
     *
     * @OA\RequestBody(
     *     description="Organizer data",
     *     @OA\JsonContent(ref=@Model(type=OrganizerDTO::class))
     * )
     * @OA\Response(
     *     response=201,
     *     description="Organizer created",
     *     @OA\JsonContent(ref=@Model(type=Organizer::class))
     * )
     */
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

    /**
     * Update an existing organizer.
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ID of the organizer to update",
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\RequestBody(
     *     description="Updated organizer data",
     *     @OA\JsonContent(ref=@Model(type=OrganizerDTO::class))
     * )
     * @OA\Response(
     *     response=200,
     *     description="Organizer updated",
     *     @OA\JsonContent(ref=@Model(type=Organizer::class))
     * )
     */
    #[Route('/{id}', methods: ['PUT'])]
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

    /**
     * Delete an organizer.
     *
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="The ID of the organizer to delete",
     *     @OA\Schema(type="integer", example=1)
     * )
     * @OA\Response(
     *     response=204,
     *     description="Organizer deleted"
     * )
     */
    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteOrganizer(Organizer $organizer): JsonResponse
    {
        $this->organizerService->deleteOrganizer($organizer);
        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * List all organizers.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a list of all organizers",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Organizer::class, groups={"full"}))
     *     )
     * )
     */
    #[Route('', methods: ['GET'])]
    public function listOrganizers(): JsonResponse
    {
        $organizers = $this->organizerService->findAll();
        return $this->json($organizers);
    }
}