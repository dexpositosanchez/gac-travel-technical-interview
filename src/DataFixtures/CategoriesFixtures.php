<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Categories;
use Symfony\Component\HttpClient\HttpClient;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://fakestoreapi.com/products/categories');
        $listado = $response->toArray();
        foreach ($listado as $data) {
            $item = new Categories();
            $item->setName($data);
            $item->setCreatedAt(new \DateTime());
            $manager->persist($item);
        }
        $manager->flush();
        
    }
}
