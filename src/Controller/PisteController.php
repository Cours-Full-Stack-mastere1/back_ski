<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Piste;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PisteController extends AbstractController
{
/**
     * Renvoie ma piste par id
     * 
     * @param Piste $piste
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/piste/{idPiste}', name: 'piste.get', methods: ['GET'])]
    #[ParamConverter("piste", options:["id" => "idPiste"])]
    public function getPisteById(
        Piste $piste,
        SerializerInterface $serializer
    ):JsonResponse
    {
        $jsonPiste = $serializer->serialize($piste, 'json', ["groups" => 'getAllPiste']);
        return new JsonResponse($jsonPiste, Response::HTTP_OK,[], true);
    }
}
