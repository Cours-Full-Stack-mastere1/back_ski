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

        //Pistes
        $piste = new Piste();
        $piste->setNom($this->faker->word());
        $piste->setCouleur($this->faker->word());
        $piste->setOuvert($this->faker->boolean());
        $piste->setLongeur($this->faker->numberBetween(1, 100));
        $temps=[1,2,3,4,5,6,7,8,9,10];
        $piste->setTemps($temps);
        

        //Stations
        $station = new Station();
        $station->setNom($this->faker->word());
        $station->setGps("{$this->faker->latitude()}, {$this->faker->longitude()}");
        $station->addPiste($piste);
        $manager->persist($piste);
        $manager->persist($station);

        $manager->flush();
    }
}
