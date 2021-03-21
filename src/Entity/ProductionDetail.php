<?php

namespace App\Entity;

use App\Repository\ProductionDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductionDetailRepository::class)
 */
class ProductionDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Production::class, inversedBy="productionDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $production;

    /**
     * @ORM\ManyToOne(targetEntity=Component::class, inversedBy="productionDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $component;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $value;

    /**
     * @ORM\OneToMany(targetEntity=ComponentOperation::class, mappedBy="productionDetail")
     */
    private $componentOperations;

    public function __construct()
    {
        $this->componentOperations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduction(): ?Production
    {
        return $this->production;
    }

    public function setProduction(?Production $production): self
    {
        $this->production = $production;

        return $this;
    }

    public function getComponent(): ?Component
    {
        return $this->component;
    }

    public function setComponent(?Component $component): self
    {
        $this->component = $component;

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
            $componentOperation->setProductionDetail($this);
        }

        return $this;
    }

    public function removeComponentOperation(ComponentOperation $componentOperation): self
    {
        if ($this->componentOperations->removeElement($componentOperation)) {
            // set the owning side to null (unless already changed)
            if ($componentOperation->getProductionDetail() === $this) {
                $componentOperation->setProductionDetail(null);
            }
        }

        return $this;
    }
}
