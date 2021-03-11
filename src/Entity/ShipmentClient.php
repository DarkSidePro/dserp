<?php

namespace App\Entity;

use App\Repository\ShipmentClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShipmentClientRepository::class)
 */
class ShipmentClient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Shipment::class, inversedBy="shipmentClients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shipment;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="shipmentClients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity=ShipmentClientDetail::class, mappedBy="shipmentClient")
     */
    private $shipmentClientDetails;

    public function __construct()
    {
        $this->shipmentClientDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    public function setShipment(?Shipment $shipment): self
    {
        $this->shipment = $shipment;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|ShipmentClientDetail[]
     */
    public function getShipmentClientDetails(): Collection
    {
        return $this->shipmentClientDetails;
    }

    public function addShipmentClientDetail(ShipmentClientDetail $shipmentClientDetail): self
    {
        if (!$this->shipmentClientDetails->contains($shipmentClientDetail)) {
            $this->shipmentClientDetails[] = $shipmentClientDetail;
            $shipmentClientDetail->setShipmentClient($this);
        }

        return $this;
    }

    public function removeShipmentClientDetail(ShipmentClientDetail $shipmentClientDetail): self
    {
        if ($this->shipmentClientDetails->removeElement($shipmentClientDetail)) {
            // set the owning side to null (unless already changed)
            if ($shipmentClientDetail->getShipmentClient() === $this) {
                $shipmentClientDetail->setShipmentClient(null);
            }
        }

        return $this;
    }
}
