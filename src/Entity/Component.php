<?php

namespace App\Entity;

use App\Repository\ComponentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ComponentRepository::class)
 */
class Component
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
    private $component_name;

    /**
     * @ORM\OneToMany(targetEntity=ComponentOperation::class, mappedBy="component")
     */
    private $componentOperations;

    /**
     * @ORM\OneToMany(targetEntity=ProductionDetail::class, mappedBy="component")
     */
    private $productionDetails;

    /**
     * @ORM\OneToMany(targetEntity=RecipeDetail::class, mappedBy="component")
     */
    private $recipeDetails;

    public function __construct()
    {
        $this->componentOperations = new ArrayCollection();
        $this->productionDetails = new ArrayCollection();
        $this->recipeDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComponentName(): ?string
    {
        return $this->component_name;
    }

    public function setComponentName(string $component_name): self
    {
        $this->component_name = $component_name;

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
            $componentOperation->setComponent($this);
        }

        return $this;
    }

    public function removeComponentOperation(ComponentOperation $componentOperation): self
    {
        if ($this->componentOperations->removeElement($componentOperation)) {
            // set the owning side to null (unless already changed)
            if ($componentOperation->getComponent() === $this) {
                $componentOperation->setComponent(null);
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
            $productionDetail->setComponent($this);
        }

        return $this;
    }

    public function removeProductionDetail(ProductionDetail $productionDetail): self
    {
        if ($this->productionDetails->removeElement($productionDetail)) {
            // set the owning side to null (unless already changed)
            if ($productionDetail->getComponent() === $this) {
                $productionDetail->setComponent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RecipeDetail[]
     */
    public function getRecipeDetails(): Collection
    {
        return $this->recipeDetails;
    }

    public function addRecipeDetail(RecipeDetail $recipeDetail): self
    {
        if (!$this->recipeDetails->contains($recipeDetail)) {
            $this->recipeDetails[] = $recipeDetail;
            $recipeDetail->setComponent($this);
        }

        return $this;
    }

    public function removeRecipeDetail(RecipeDetail $recipeDetail): self
    {
        if ($this->recipeDetails->removeElement($recipeDetail)) {
            // set the owning side to null (unless already changed)
            if ($recipeDetail->getComponent() === $this) {
                $recipeDetail->setComponent(null);
            }
        }

        return $this;
    }
}
