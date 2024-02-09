<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\OneToMany(targetEntity: Articles::class, mappedBy: 'categories')]
    private Collection $nombreArticles;

    public function __construct()
    {
        $this->nombreArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getNombreArticles(): Collection
    {
        return $this->nombreArticles;
    }

    public function addNombreArticle(Articles $nombreArticle): static
    {
        if (!$this->nombreArticles->contains($nombreArticle)) {
            $this->nombreArticles->add($nombreArticle);
            $nombreArticle->setCategories($this);
        }

        return $this;
    }

    public function removeNombreArticle(Articles $nombreArticle): static
    {
        if ($this->nombreArticles->removeElement($nombreArticle)) {
            // set the owning side to null (unless already changed)
            if ($nombreArticle->getCategories() === $this) {
                $nombreArticle->setCategories(null);
            }
        }

        return $this;
    }
}
