<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Categories;
use App\Entity\Products;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\Persistence\ManagerRegistry;

class ProductsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://fakestoreapi.com/products');
        $listado = $response->toArray();
        foreach ($listado as $data) {
            $item = new Products();
            $item->setName($data['title']);
            $item->setCreatedAt(new \DateTime());
            $item->setStock($data['rating']['count']);
            $category = new Categories();
            $category = $manager
            ->getRepository(Categories::class)
            ->findBy(array('name' => $data['category']));
            $item->setCategory($category[0]);
            $manager->persist($item);
        }
        $manager->flush();
    }
}
