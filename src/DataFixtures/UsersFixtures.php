<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Users;
use Symfony\Component\HttpClient\HttpClient;

class UsersFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://fakestoreapi.com/users');
        $listado = $response->toArray();
        foreach ($listado as $data) {
            $item = new Users();
            $item->setUsername($data['username']);
            $item->setPassword($data['password']);
            $item->setActive(true);
            $item->setCreatedAt(new \DateTime());
            $manager->persist($item);
        }
        $manager->flush();
    }
}
