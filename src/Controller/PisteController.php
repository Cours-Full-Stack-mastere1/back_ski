<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Piste;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * creer une Piste
     * 
     * @param Piste $piste
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/piste', name: 'piste.create', methods: ["POST"])]
    public function createStationEntry(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ):JsonResponse
    {
        $piste = $serializer->deserialize($request->getContent(), Piste::class,'json');
        $errors = $validator->validate($piste);
        if(count($errors) > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST,[], true);
        }
        $entityManager->persist($piste);
        $entityManager->flush();
        $jsonPiste = $serializer->serialize($piste, 'json', ["groups" => "getAllPiste"]);
        $location = $urlGenerator->generate("piste.get", ["idPiste" => $piste->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonPiste, Response::HTTP_CREATED,["Location" => $location], true);
    }
}
