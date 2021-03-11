<?php

namespace App\Entity;

use App\Repository\ProductionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductionRepository::class)
 */
class Production
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="productions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="productions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipe;

    /**
     * @ORM\Column(type="boolean")
     */
    private $modification;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datestamp;

    /**
     * @ORM\OneToMany(targetEntity=ComponentOperation::class, mappedBy="Production")
     */
    private $componentOperations;

    /**
     * @ORM\OneToMany(targetEntity=ProductionDetail::class, mappedBy="production")
     */
    private $productionDetails;

    /**
     * @ORM\OneToMany(targetEntity=ProductOperation::class, mappedBy="Production")
     */
    private $productOperations;

    public function __construct()
    {
        $this->componentOperations = new ArrayCollection();
        $this->productionDetails = new ArrayCollection();
        $this->productOperations = new ArrayCollection();
    }

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

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getModification(): ?bool
    {
        return $this->modification;
    }

    public function setModification(bool $modification): self
    {
        $this->modification = $modification;

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
            $componentOperation->setProduction($this);
        }

        return $this;
    }

    public function removeComponentOperation(ComponentOperation $componentOperation): self
    {
        if ($this->componentOperations->removeElement($componentOperation)) {
            // set the owning side to null (unless already changed)
            if ($componentOperation->getProduction() === $this) {
                $componentOperation->setProduction(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductionDetail[]
     */
    public function getProductionDetails(): Collection
    {
        return $this->productionDetails;
    }

    public function addProductionDetail(ProductionDetail $productionDetail): self
    {
        if (!$this->productionDetails->contains($productionDetail)) {
            $this->productionDetails[] = $productionDetail;
            $productionDetail->setProduction($this);
        }

        return $this;
    }

    public function removeProductionDetail(ProductionDetail $productionDetail): self
    {
        if ($this->productionDetails->removeElement($productionDetail)) {
            // set the owning side to null (unless already changed)
            if ($productionDetail->getProduction() === $this) {
                $productionDetail->setProduction(null);
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
            $productOperation->setProduction($this);
        }

        return $this;
    }

    public function removeProductOperation(ProductOperation $productOperation): self
    {
        if ($this->productOperations->removeElement($productOperation)) {
            // set the owning side to null (unless already changed)
            if ($productOperation->getProduction() === $this) {
                $productOperation->setProduction(null);
            }
        }

        return $this;
    }
}
