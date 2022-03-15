<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\StockHistoric;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Products
 *
 * @ORM\Table(name="products", indexes={@ORM\Index(name="category_id", columns={"category_id"})})
 * @ORM\Entity
 */
class Products
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="stock", type="integer", nullable=false)
     */
    private $stock;

    private $stockFinal;

    /**
     * @var \Categories
     *
     * @ORM\ManyToOne(targetEntity="Categories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * })
     */
    private $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function __toString() {
        return $this->name;
    }

    public function getStockFinal(): ?int
    {
        return $this->stockFinal;
    }

    public function setStockFinal(EntityManagerInterface $entityManager): self
    {
        $stockHistorics = $entityManager
            ->getRepository(StockHistoric::class)
            ->findBy(array('product' => $this));
        $listado=array();
        $this->stockFinal=$this->getStock();
        foreach ($stockHistorics as $item) {
            $this->stockFinal=$this->stockFinal+$item->getStock();
        }
        return $this;
    }


}
