<?php

namespace App\Entity;

use App\Repository\ShipmentClientDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShipmentClientDetailRepository::class)
 */
class ShipmentClientDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ShipmentClient::class, inversedBy="shipmentClientDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shipmentClient;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="shipmentClientDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShipmentClient(): ?ShipmentClient
    {
        return $this->shipmentClient;
    }

    public function setShipmentClient(?ShipmentClient $shipmentClient): self
    {
        $this->shipmentClient = $shipmentClient;

        return $this;
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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
