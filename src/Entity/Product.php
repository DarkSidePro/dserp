<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Animal::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $animal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $product_name;

    /**
     * @ORM\OneToMany(targetEntity=ProductOperation::class, mappedBy="product")
     */
    private $productOperations;

    /**
     * @ORM\OneToMany(targetEntity=ShipmentClientDetail::class, mappedBy="product")
     */
    private $shipmentClientDetails;

    /**
     * @ORM\OneToMany(targetEntity=Recipe::class, mappedBy="product")
     */
    private $recipes;

    /**
     * @ORM\OneToMany(targetEntity=Production::class, mappedBy="product")
     */
    private $productions;

    public function __construct()
    {
        $this->productOperations = new ArrayCollection();
        $this->shipmentClientDetails = new ArrayCollection();
        $this->recipes = new ArrayCollection();
        $this->productions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): self
    {
        $this->animal = $animal;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->product_name;
    }

    public function setProductName(string $product_name): self
    {
        $this->product_name = $product_name;

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
            $productOperation->setProduct($this);
        }

        return $this;
    }

    public function removeProductOperation(ProductOperation $productOperation): self
    {
        if ($this->productOperations->removeElement($productOperation)) {
            // set the owning side to null (unless already changed)
            if ($productOperation->getProduct() === $this) {
                $productOperation->setProduct(null);
            }
        }

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
            $shipmentClientDetail->setProduct($this);
        }

        return $this;
    }

    public function removeShipmentClientDetail(ShipmentClientDetail $shipmentClientDetail): self
    {
        if ($this->shipmentClientDetails->removeElement($shipmentClientDetail)) {
            // set the owning side to null (unless already changed)
            if ($shipmentClientDetail->getProduct() === $this) {
                $shipmentClientDetail->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Recipe[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->setProduct($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        if ($this->recipes->removeElement($recipe)) {
            // set the owning side to null (unless already changed)
            if ($recipe->getProduct() === $this) {
                $recipe->setProduct(null);
            }
        }

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
            $production->setProduct($this);
        }

        return $this;
    }

    public function removeProduction(Production $production): self
    {
        if ($this->productions->removeElement($production)) {
            // set the owning side to null (unless already changed)
            if ($production->getProduct() === $this) {
                $production->setProduct(null);
            }
        }

        return $this;
    }
}
