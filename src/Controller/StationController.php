<?php

namespace App\Controller;

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
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class StationController extends AbstractController
{
    /**
     * Renvoie mes stations
     * 
     * @param StationRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
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
     * @param SerializerInterface $serializer
     * @return JsonResponse
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
     * @param Station $station
     * @param SerializerInterface $serializer
     * @return JsonResponse
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
        //dd($station);
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
}
