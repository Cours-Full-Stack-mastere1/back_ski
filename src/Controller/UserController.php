<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\User;

class UserController extends AbstractController
{
    /**
     * Renvoie l utilisateur courant
     * 
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * 
     * @OA\Get(
     *      path="/api/user",
     *      tags={"Users"},
     *      summary="Récupère toutes les stations",
     *      @OA\Response(
     *          response=200,
     *          description="Renvoie l utilisateur courant",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=User::class, groups={"getUser"}))
     *          )
     *      )
     * )
     * 
     */
    #[Route('/api/user', name: 'user.get', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function getUserProfile(
        SerializerInterface $serializer
    ):JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $context = SerializationContext::create()->setGroups(["getUser"]);
        $jsonUser = $serializer->serialize($user, 'json', $context);
        return new JsonResponse($jsonUser, Response::HTTP_OK,[], true);
    }
}
