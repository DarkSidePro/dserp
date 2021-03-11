<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $client_name;

    /**
     * @ORM\OneToMany(targetEntity=ShipmentClient::class, mappedBy="client")
     */
    private $shipmentClients;

    public function __construct()
    {
        $this->shipmentClients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientName(): ?string
    {
        return $this->client_name;
    }

    public function setClientName(string $client_name): self
    {
        $this->client_name = $client_name;

        return $this;
    }

    /**
     * @return Collection|ShipmentClient[]
     */
    public function getShipmentClients(): Collection
    {
        return $this->shipmentClients;
    }

    public function addShipmentClient(ShipmentClient $shipmentClient): self
    {
        if (!$this->shipmentClients->contains($shipmentClient)) {
            $this->shipmentClients[] = $shipmentClient;
            $shipmentClient->setClient($this);
        }

        return $this;
    }

    public function removeShipmentClient(ShipmentClient $shipmentClient): self
    {
        if ($this->shipmentClients->removeElement($shipmentClient)) {
            // set the owning side to null (unless already changed)
            if ($shipmentClient->getClient() === $this) {
                $shipmentClient->setClient(null);
            }
        }

        return $this;
    }
}
