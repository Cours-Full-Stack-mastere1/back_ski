<?php

namespace App\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\Serializer\SerializerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use App\Entity\Station;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\StationRepository;
use JMS\Serializer\DeserializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Entity\Piste;

class StationController extends AbstractController
{
    /**
     * Renvoie mes stations
     * 
     * @param StationRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * 
     * @OA\Get(
     *      path="/api/station",
     *      tags={"Stations"},
     *      summary="Récupère toutes les stations",
     *      @OA\Response(
     *          response=200,
     *          description="Renvoie toutes les stations",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref=@Model(type=Station::class, groups={"getAllStation"}))
     *          )
     *      )
     * )
     */
    
    #[Route('/api/station', name: 'station.getAll', methods: ['GET'])]
    public function getStation(
        StationRepository $repository,
        SerializerInterface $serializer
    ):JsonResponse
    {
        $station = $repository->findAll();
        $context = SerializationContext::create()->setGroups(["getAllStation"]);
        $jsonStation = $serializer->serialize($station, 'json', $context);
        return new JsonResponse($jsonStation, Response::HTTP_OK,[], true);
    }

    /**
     * Renvoie ma Station par id
     * 
     * @param Station $station
     * @param int $idStation
     * @param TagAwareCacheInterface $cache
     * @param StationRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * 
     * @OA\Get(
     *     path="/api/station/{idStation}",
     *    tags={"Stations"},
     *   summary="Récupère une station par son id",
     *  @OA\Parameter(
     *     name="idStation",
     *   in="path",
     *  description="id de la station",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     *    response=200,
     *  description="Renvoie une station",
     * @OA\JsonContent(ref=@Model(type=Station::class, groups={"getAllStation"}))
     * )
     * )
     * 
     */
    #[Route('/api/station/{idStation}', name: 'station.get', methods: ['GET'])]
    #[ParamConverter("station", options:["id" => "idStation"])]
    public function getStationById(
        Station $station,        
        int $idStation,
        TagAwareCacheInterface $cache,
        StationRepository $repository,
        SerializerInterface $serializer
    ):JsonResponse
    {        
        $idCache = "getStation".$station->getId();
        $jsonStation = $cache->get($idCache, function(ItemInterface $item) use ($repository, $serializer, $idStation){
            //$serializer->serialize($station, 'json', $context);
            $item->tag("stationTag");
            $station = $repository->find($idStation);
            $context= SerializationContext::create()->setGroups(["getAllStation"]);
            return $serializer->serialize($station, 'json', $context);
        });
        return new JsonResponse($jsonStation, Response::HTTP_OK,[], true);
    }

    /**
     * creer une Station
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * 
     * @OA\Post(
     *    path="/api/station",
     *  tags={"Stations"},
     *  summary="Créer une station",
     * @OA\RequestBody(
     *   description="Objet Station à envoyer",
     * required=true,
     * @OA\JsonContent(ref=@Model(type=Station::class, groups={"getAllStation"}))
     * ),
     * @OA\Response(
     *   response=201,
     * description="Renvoie une station",
     * @OA\JsonContent(ref=@Model(type=Station::class, groups={"getAllStation"}))
     * )
     * )
     * 
     */
    #[Route('/api/station', name: 'station.create', methods: ["POST"])]
    public function createStationEntry(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ):JsonResponse
    {
        $station = $serializer->deserialize($request->getContent(), Station::class,'json');
        $errors = $validator->validate($station);
        if(count($errors) > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST,[], true);
        }
        $entityManager->persist($station);
        $entityManager->flush();
        $context= SerializationContext::create()->setGroups(["getAllStation"]);
        $jsonStation = $serializer->serialize($station, 'json', $context);
        $location = $urlGenerator->generate("station.get", ["idStation" => $station->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonStation, Response::HTTP_CREATED,["Location" => $location], true);
    }

    /**
     * Met à jour une Station
     * 
     * @param Station $station
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param StationRepository $repository
     * @return JsonResponse
     * 
     * @OA\Put(
     *    path="/api/station/{idStation}",
     * tags={"Stations"},
     * summary="Met à jour une station",
     * @OA\Parameter(
     *   name="idStation",
     * in="path",
     * description="id de la station",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     *  description="Objet Station à envoyer",
     * required=true,
     * @OA\JsonContent(ref=@Model(type=Station::class, groups={"getAllStation"}))
     * ),
     * @OA\Response(
     * response=204,
     * description="Met à jour une station",
     * @OA\JsonContent(ref=@Model(type=Station::class, groups={"getAllStation"}))
     * )
     * )
     * 
     */
    #[Route("/api/station/{idStation}", name:"station.update", methods: ["PUT"])]
    #[ParamConverter("station", options:["id" => "idStation"])]
    public function updateStation(
        Station $station,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        StationRepository $repository
    ):JsonResponse
    {
        $context = DeserializationContext::create()->setAttribute(AbstractNormalizer::OBJECT_TO_POPULATE, $station);
        $updatedStation = $serializer->deserialize($request->getContent(),Station::class,'json',$context);
        
        $content = $request->toArray();
        /* if(isset($content["idDevolution"])){
                $idDevolutions = $content["idDevolution"];
                foreach ($updatedPokedex->getDevolutionId() as $key => $devolutions_Id) {
                    $updatedPokedex->removeDevolutionId($devolutions_Id);
                }
                $relatedEntity = $repository->find($idDevolutions);
                $updatedPokedex->addDevolutionId($relatedEntity);
            } */
        // $updatedPokedex->addDevolutionId($idDevolution);

        $entityManager->persist($updatedStation);
        $entityManager->flush();
        
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);

    }

    /**
     * Supprime une Station
     * 
     * @param Station $station
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * 
     * @OA\Delete(
     *   path="/api/station/{idStation}",
     * tags={"Stations"},
     * summary="Supprime une Station",
     * @OA\Parameter(
     *  name="idStation",
     * in="path",
     * description="id de la station",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="Supprime une station",
     * @OA\JsonContent(ref=@Model(type=Station::class, groups={"getAllStation"}))
     * )
     * )
     *  
     */
    #[Route("/api/station/delete/{idStation}", name:"station.delete", methods: ["DELETE"])]
    #[ParamConverter("station", options:["id" => "idStation"])]
    public function forcDeleteFromStation(
        Station $station, 
        EntityManagerInterface $entityManager
        ): JsonResponse
    {
        $entityManager->remove($station);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Renvoie toutes les pistes d'une station
     * 
     * @param Station $station
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * 
     * @OA\Get(
     *    path="/api/station/{idStation}/piste",
     * tags={"Stations"},
     * summary="Récupère toutes les pistes d'une station",
     * @OA\Parameter(
     * name="idStation",
     * in="path",
     * description="id de la station",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Renvoie toutes les pistes d'une station",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref=@Model(type=Piste::class, groups={"getAllPiste"}))
     * )
     * )
     * )
     * 
     */
    #[Route("/api/station/{idStation}/piste", name:"station.piste", methods: ["GET"])]
    #[ParamConverter("station", options:["id" => "idStation"])]
    public function getPisteFromStation(
        Station $station,
        SerializerInterface $serializer
    ):JsonResponse
    {
        $context = SerializationContext::create()->setGroups(["getAllPiste"]);
        $jsonPiste = $serializer->serialize($station->getPiste()->toArray(), 'json', $context);
        return new JsonResponse($jsonPiste, Response::HTTP_OK,[], true);
    }

    /**
     * Renvoie une piste d'une station
     * 
     * @param Station $station
     * @param Piste $piste
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * 
     * @OA\Get(
     *   path="/api/station/{idStation}/piste/{idPiste}",
     * tags={"Stations"},
     * summary="Récupère une piste d'une station",
     * @OA\Parameter(
     * name="idStation",
     * in="path",
     * description="id de la station",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="idPiste",
     * in="path",
     * description="id de la piste",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Renvoie une piste d'une station",
     * @OA\JsonContent(ref=@Model(type=Piste::class, groups={"getAllPiste"}))
     * )
     * )
     * 
     */
    #[Route("/api/station/{idStation}/piste/{idPiste}", name:"station.piste.get", methods: ["GET"])]
    #[ParamConverter("station", options:["id" => "idStation"])]
    #[ParamConverter("piste", options:["id" => "idPiste"])]
    public function getPisteFromStationById(
        Station $station,
        Piste $piste,
        SerializerInterface $serializer
    ):JsonResponse
    {
        $context = SerializationContext::create()->setGroups(["getAllPiste"]);
        $jsonPiste = $serializer->serialize($piste, 'json', $context);
        return new JsonResponse($jsonPiste, Response::HTTP_OK,[], true);
    }

    /**
     * Ajoute une piste à une station
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @param Station $station
     * @return JsonResponse
     * 
     * @OA\Post(
     *  path="/api/station/{idStation}/piste",
     * tags={"Stations"},
     * summary="Ajoute une piste à une station",
     * @OA\Parameter(
     * name="idStation",
     * in="path",
     * description="id de la station",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * description="Objet Piste à envoyer",
     * required=true,
     * @OA\JsonContent(ref=@Model(type=Piste::class, groups={"getAllPiste"}))
     * ),
     * @OA\Response(
     * response=201,
     * description="Ajoute une piste à une station",
     * @OA\JsonContent(ref=@Model(type=Piste::class, groups={"getAllPiste"}))
     * )
     * )
     * 
     * 
     */
    #[Route("/api/station/{idStation}/piste", name:"station.piste.create", methods: ["POST"])]
    #[ParamConverter("station", options:["id" => "idStation"])]
    public function createPisteEntry(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        Station $station
    ):JsonResponse
    {
        $piste = $serializer->deserialize($request->getContent(), Piste::class,'json');
        $errors = $validator->validate($piste);
        if(count($errors) > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST,[], true);
        }
        $station->addPiste($piste);
        $entityManager->persist($piste);
        $entityManager->flush();
        $context= SerializationContext::create()->setGroups(["getAllPiste"]);
        $jsonPiste = $serializer->serialize($piste, 'json', $context);
        $location = $urlGenerator->generate("station.piste.get", ["idStation" => $station->getId(), "idPiste" => $piste->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonPiste, Response::HTTP_CREATED,["Location" => $location], true);
    }

    /**
     * Supprime une piste d'une station
     * 
     * @param Station $station
     * @param Piste $piste
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * 
     * @OA\Delete(
     *   path="/api/station/{idStation}/piste/{idPiste}",
     * tags={"Stations"},
     * summary="Supprime une piste d'une station",
     * @OA\Parameter(
     * name="idStation",
     * in="path",
     * description="id de la station",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="idPiste",
     * in="path",
     * description="id de la piste",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="Supprime une piste d'une station",
     * @OA\JsonContent(ref=@Model(type=Piste::class, groups={"getAllPiste"}))
     * )
     * )
     * 
     */
    #[Route("/api/station/{idStation}/piste/{idPiste}", name:"station.piste.delete", methods: ["DELETE"])]
    #[ParamConverter("station", options:["id" => "idStation"])]
    #[ParamConverter("piste", options:["id" => "idPiste"])]
    public function deletePisteFromStation(
        Station $station,
        Piste $piste,
        EntityManagerInterface $entityManager
    ):JsonResponse
    {
        $station->removePiste($piste);
        $entityManager->remove($piste);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Met à jour une piste d'une station
     * 
     * @param Station $station
     * @param Piste $piste
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param StationRepository $repository
     * @return JsonResponse
     * 
     * @OA\Put(
     *   path="/api/station/{idStation}/piste/{idPiste}",
     * tags={"Stations"},
     * summary="Met à jour une piste d'une station",
     * @OA\Parameter(
     * name="idStation",
     * in="path",
     * description="id de la station",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Parameter(
     * name="idPiste",
     * in="path",
     * description="id de la piste",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * description="Objet Piste à envoyer",
     * required=true,
     * @OA\JsonContent(ref=@Model(type=Piste::class, groups={"getAllPiste"}))
     * ),
     * @OA\Response(
     * response=204,
     * description="Met à jour une piste d'une station",
     * @OA\JsonContent(ref=@Model(type=Piste::class, groups={"getAllPiste"}))
     * )
     * )
     * 
     */
    #[Route("/api/station/{idStation}/piste/{idPiste}", name:"station.piste.update", methods: ["PUT"])]
    #[ParamConverter("station", options:["id" => "idStation"])]
    #[ParamConverter("piste", options:["id" => "idPiste"])]
    public function updatePisteFromStation(
        Station $station,
        Piste $piste,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        StationRepository $repository
    ):JsonResponse
    {
        $context = DeserializationContext::create()->setAttribute(AbstractNormalizer::OBJECT_TO_POPULATE, $piste);
        $updatedPiste = $serializer->deserialize($request->getContent(),Piste::class,'json',$context);
        
        $content = $request->toArray();
        /* if(isset($content["idDevolution"])){
                $idDevolutions = $content["idDevolution"];
                foreach ($updatedPokedex->getDevolutionId() as $key => $devolutions_Id) {
                    $updatedPokedex->removeDevolutionId($devolutions_Id);
                }
                $relatedEntity = $repository->find($idDevolutions);
                $updatedPokedex->addDevolutionId($relatedEntity);
            } */
        // $updatedPokedex->addDevolutionId($idDevolution);

        $entityManager->persist($updatedPiste);
        $entityManager->flush();
        
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);

    }

    /**
     * Renvoie si une station est ouverte
     * 
     * @param Station $station
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * 
     * @OA\Get(
     *  path="/api/station/{idStation}/isOpened",
     * tags={"Stations"},
     * summary="Renvoie si une station est ouverte",
     * @OA\Parameter(
     * name="idStation",
     * in="path",
     * description="id de la station",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Renvoie si une station est ouverte",
     * @OA\JsonContent(
    *         type="object",
    *         @OA\Property(
    *             property="ouvert",
    *             type="boolean"
    *         )
    *     )
     * )
     * )
     * 
     */
    #[Route("/api/station/{idStation}/isOpened", name:"station.isOpened", methods: ["GET"])]
    #[ParamConverter("station", options:["id" => "idStation"])]
    public function isOpened(
        Station $station,
        SerializerInterface $serializer
    ):JsonResponse
    {
        $context = SerializationContext::create()->setGroups(["getAllPiste"]);
        $jsonPiste = $serializer->serialize($station->getPiste()->toArray(), 'json', $context);
        $pistes = json_decode($jsonPiste);
        $isOpened = true;
        foreach ($pistes as $key => $piste) {
            if($piste->ouvert == false){
                $isOpened = false;
            }
        }
        //$jsonOpen= $serializer->serialize($isOpened, 'json');
        $jsonOpen = json_encode(["ouvert" => $isOpened]);
        return new JsonResponse($jsonOpen, Response::HTTP_OK,[], true);
    }
}
