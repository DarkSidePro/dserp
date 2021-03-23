<?php

namespace App\Entity;

use App\Repository\ShipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ShipmentRepository::class)
 */
class Shipment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datestamp;

    /**
     * @ORM\OneToMany(targetEntity=ShipmentClient::class, mappedBy="shipment")
     */
    private $shipmentClients;

    /**
     * @ORM\OneToMany(targetEntity=ComponentOperation::class, mappedBy="shipment_id")
     */
    private $componentOperations;

    /**
     * @ORM\OneToMany(targetEntity=ProductOperation::class, mappedBy="shipment_id")
     */
    private $productOperations;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $modification;

    public function __construct()
    {
        $this->shipmentClients = new ArrayCollection();
        $this->componentOperations = new ArrayCollection();
        $this->productOperations = new ArrayCollection();
        $this->datestamp = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $shipmentClient->setShipment($this);
        }

        return $this;
    }

    public function removeShipmentClient(ShipmentClient $shipmentClient): self
    {
        if ($this->shipmentClients->removeElement($shipmentClient)) {
            // set the owning side to null (unless already changed)
            if ($shipmentClient->getShipment() === $this) {
                $shipmentClient->setShipment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ComponentOperation[]
     */
    public function getComponentOperations(): Collection
    {
        return $this->componentOperations;
    }

    public function addComponentOperation(ComponentOperation $componentOperation): self
    {
        if (!$this->componentOperations->contains($componentOperation)) {
            $this->componentOperations[] = $componentOperation;
            $componentOperation->setShipmentId($this);
        }

        return $this;
    }

    public function removeComponentOperation(ComponentOperation $componentOperation): self
    {
        if ($this->componentOperations->removeElement($componentOperation)) {
            // set the owning side to null (unless already changed)
            if ($componentOperation->getShipmentId() === $this) {
                $componentOperation->setShipmentId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductOperation[]
     */
    public function getProductOperations(): Collection
    {
        return $this->productOperations;
    }

    public function addProductOperation(ProductOperation $productOperation): self
    {
        if (!$this->productOperations->contains($productOperation)) {
            $this->productOperations[] = $productOperation;
            $productOperation->setShipmentId($this);
        }

        return $this;
    }

    public function removeProductOperation(ProductOperation $productOperation): self
    {
        if ($this->productOperations->removeElement($productOperation)) {
            // set the owning side to null (unless already changed)
            if ($productOperation->getShipmentId() === $this) {
                $productOperation->setShipmentId(null);
            }
        }

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getModification(): ?bool
    {
        return $this->modification;
    }

    public function setModification(?bool $modification): self
    {
        $this->modification = $modification;

        return $this;
    }
}
