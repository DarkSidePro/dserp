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
    private $dispatch;

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
     * @ORM\ManyToOne(targetEntity=Production::class, inversedBy="productOperations")
     */
    private $production_id;

    /**
     * @ORM\ManyToOne(targetEntity=Shipment::class, inversedBy="productOperations")
     */
    private $shipment_id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $shipment;

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

    public function getDispatch(): ?string
    {
        return $this->dispatch;
    }

    public function setDispatch(?string $dispatch): self
    {
        $this->dispatch = $dispatch;

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

    public function getProductionId(): ?Production
    {
        return $this->production_id;
    }

    public function setProductionId(?Production $production_id): self
    {
        $this->production_id = $production_id;

        return $this;
    }

    public function getShipmentId(): ?Shipment
    {
        return $this->shipment_id;
    }

    public function setShipmentId(?Shipment $shipment_id): self
    {
        $this->shipment_id = $shipment_id;

        return $this;
    }

    public function getShipment(): ?string
    {
        return $this->shipment;
    }

    public function setShipment(?string $shipment): self
    {
        $this->shipment = $shipment;

        return $this;
    }
}
