<?php

namespace App\Entity;

use App\Repository\ProductOperationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductOperationRepository::class)
 */
class ProductOperation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="productOperations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $enter;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $exit;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $modification;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $production;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $state;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datestamp;

    /**
     * @ORM\ManyToOne(targetEntity=Shipment::class, inversedBy="productOperations")
     */
    private $Shipment;

    /**
     * @ORM\ManyToOne(targetEntity=Production::class, inversedBy="productOperations")
     */
    private $Production;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getEnter(): ?string
    {
        return $this->enter;
    }

    public function setEnter(?string $enter): self
    {
        $this->enter = $enter;

        return $this;
    }

    public function getExit(): ?string
    {
        return $this->exit;
    }

    public function setExit(?string $exit): self
    {
        $this->exit = $exit;

        return $this;
    }

    public function getModification(): ?string
    {
        return $this->modification;
    }

    public function setModification(?string $modification): self
    {
        $this->modification = $modification;

        return $this;
    }

    public function getProduction(): ?string
    {
        return $this->production;
    }

    public function setProduction(?string $production): self
    {
        $this->production = $production;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getDatestamp(): ?\DateTimeInterface
    {
        return $this->datestamp;
    }

    public function setDatestamp(\DateTimeInterface $datestamp): self
    {
        $this->datestamp = $datestamp;

        return $this;
    }

    public function getShipment(): ?Shipment
    {
        return $this->Shipment;
    }

    public function setShipment(?Shipment $Shipment): self
    {
        $this->Shipment = $Shipment;

        return $this;
    }
}
