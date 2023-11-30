<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

use App\Entity\Piste;
use App\Entity\Station;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class AppFixtures extends Fixture
{
    /**
     * Password Hasher
     *
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;
    private Generator $faker;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->faker = Factory::create();
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];

        //Set Public User
        $publicUser = new User();
        $publicUser->setUsername("public");
        $publicUser->setRoles(["PUBLIC"]);
        $publicUser->setPassword($this->userPasswordHasher->hashPassword($publicUser, "public"));
        $manager->persist($publicUser);
        $users[] = $publicUser;

        // Authentifiés
        for ($i = 0; $i < 5; $i++) {
            $userUser = new User();
            $password = $this->faker->password(2, 6);
            $userUser->setUsername($this->faker->userName() . "@". $password);
            $userUser->setRoles(["USER"]);
            $userUser->setPassword($this->userPasswordHasher->hashPassword($userUser, $password));
            $manager->persist($userUser);
            $users[] = $userUser;

        }


        // Admins
        $adminUser = new User();
        $adminUser->setUsername("admin");
        $adminUser->setRoles(["ADMIN"]);
        $adminUser->setPassword($this->userPasswordHasher->hashPassword($adminUser, "password"));
        $manager->persist($adminUser);
        $users[] = $adminUser;

        // Les Deux Alpes
    $stationDeuxAlpes = new Station();
    $stationDeuxAlpes->setNom("Les Deux Alpes");
    $stationDeuxAlpes->setGps("45.0014, 6.1231");

    for ($i = 0; $i < 10; $i++) {
        $piste = new Piste();
        if ($i < 3) {
            $piste->setNom("Piste Facile " . ($i + 1));
            $piste->setCouleur("Verte");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        } elseif ($i < 7) {
            $piste->setNom("Piste Intermédiaire " . ($i - 2));
            $piste->setCouleur("Bleue");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        } else {
            $piste->setNom("Piste Difficile " . ($i - 6));
            $piste->setCouleur("Noire");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        }
        $stationDeuxAlpes->addPiste($piste);
        $manager->persist($piste);
    }

    $manager->persist($stationDeuxAlpes);

    // Alpe d'Huez
    $stationAlpeHuez = new Station();
    $stationAlpeHuez->setNom("Alpe d'Huez");
    $stationAlpeHuez->setGps("45.0931, 6.0698");

    for ($i = 0; $i < 10; $i++) {
        $piste = new Piste();
        if ($i < 2) {
            $piste->setNom("Piste Facile " . ($i + 1));
            $piste->setCouleur("Verte");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        } elseif ($i < 7) {
            $piste->setNom("Piste Intermédiaire " . ($i - 1));
            $piste->setCouleur("Bleue");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        } else {
            $piste->setNom("Piste Difficile " . ($i - 6));
            $piste->setCouleur("Noire");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        }
        $stationAlpeHuez->addPiste($piste);
        $manager->persist($piste);
    }

    $manager->persist($stationAlpeHuez);

    // Chamrousse
    $stationChamrousse = new Station();
    $stationChamrousse->setNom("Chamrousse");
    $stationChamrousse->setGps("45.1137, 5.8754");

    for ($i = 0; $i < 10; $i++) {
        $piste = new Piste();
        if ($i < 4) {
            $piste->setNom("Piste Facile " . ($i + 1));
            $piste->setCouleur("Verte");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        } elseif ($i < 7) {
            $piste->setNom("Piste Intermédiaire " . ($i - 3));
            $piste->setCouleur("Bleue");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        } else {
            $piste->setNom("Piste Difficile " . ($i - 6));
            $piste->setCouleur("Noire");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        }
        $stationChamrousse->addPiste($piste);
        $manager->persist($piste);
    }

    $manager->persist($stationChamrousse);


    // Villard-de-Lans / Corrençon-en-Vercors
    $stationVillardCorrencon = new Station();
    $stationVillardCorrencon->setNom("Villard-de-Lans / Corrençon-en-Vercors");
    $stationVillardCorrencon->setGps("45.0789, 5.5529");

    for ($i = 0; $i < 10; $i++) {
        $piste = new Piste();
        if ($i < 6) {
            $piste->setNom("Piste Facile " . ($i + 1));
            $piste->setCouleur("Verte");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        } else {
            $piste->setNom("Piste Intermédiaire " . ($i - 5));
            $piste->setCouleur("Bleue");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        }
        $stationVillardCorrencon->addPiste($piste);
        $manager->persist($piste);
    }

    $manager->persist($stationVillardCorrencon);


    // Autrans-Méaudre en Vercors
    $stationAutransMeaudre = new Station();
    $stationAutransMeaudre->setNom("Autrans-Méaudre en Vercors");
    $stationAutransMeaudre->setGps("45.1796, 5.5311");

    for ($i = 0; $i < 10; $i++) {
        $piste = new Piste();
        if ($i < 3) {
            $piste->setNom("Piste Facile " . ($i + 1));
            $piste->setCouleur("Verte");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        } elseif ($i < 7) {
            $piste->setNom("Piste Intermédiaire " . ($i - 2));
            $piste->setCouleur("Bleue");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        } else {
            $piste->setNom("Piste Difficile " . ($i - 6));
            $piste->setCouleur("Noire");
            $piste->setOuvert(true);
            $piste->setLongeur(rand(1, 100));
            $piste->setTemps(rand(1, 50));
        }
        $stationAutransMeaudre->addPiste($piste);
        $manager->persist($piste);
    }

    $manager->persist($stationAutransMeaudre);

    $manager->flush();
    }
}
