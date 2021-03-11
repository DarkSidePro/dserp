<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecipeRepository::class)
 */
class Recipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $recipe_name;

    /**
     * @ORM\OneToMany(targetEntity=Production::class, mappedBy="recipe")
     */
    private $productions;

    /**
     * @ORM\OneToMany(targetEntity=RecipeDetail::class, mappedBy="recipe")
     */
    private $recipeDetails;

    public function __construct()
    {
        $this->productions = new ArrayCollection();
        $this->recipeDetails = new ArrayCollection();
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

    public function getRecipeName(): ?string
    {
        return $this->recipe_name;
    }

    public function setRecipeName(string $recipe_name): self
    {
        $this->recipe_name = $recipe_name;

        return $this;
    }

    /**
     * @return Collection|Production[]
     */
    public function getProductions(): Collection
    {
        return $this->productions;
    }

    public function addProduction(Production $production): self
    {
        if (!$this->productions->contains($production)) {
            $this->productions[] = $production;
            $production->setRecipe($this);
        }

        return $this;
    }

    public function removeProduction(Production $production): self
    {
        if ($this->productions->removeElement($production)) {
            // set the owning side to null (unless already changed)
            if ($production->getRecipe() === $this) {
                $production->setRecipe(null);
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
            $recipeDetail->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeDetail(RecipeDetail $recipeDetail): self
    {
        if ($this->recipeDetails->removeElement($recipeDetail)) {
            // set the owning side to null (unless already changed)
            if ($recipeDetail->getRecipe() === $this) {
                $recipeDetail->setRecipe(null);
            }
        }

        return $this;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }
}
