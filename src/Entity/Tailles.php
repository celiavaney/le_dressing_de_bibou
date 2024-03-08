<?php

namespace App\Entity;

use App\Repository\TaillesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaillesRepository::class)]
class Tailles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\OneToMany(targetEntity: Articles::class, mappedBy: 'tailles')]
    private Collection $articles;

    #[ORM\ManyToMany(targetEntity: Enfants::class, inversedBy: 'tailles')]
    private Collection $enfants;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->enfants = new ArrayCollection();
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
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setTailles($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getTailles() === $this) {
                $article->setTailles(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Enfants>
     */
    public function getEnfants(): Collection
    {
        return $this->enfants;
    }

    public function addEnfant(Enfants $enfant): static
    {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants->add($enfant);
        }

        return $this;
    }

    public function removeEnfant(Enfants $enfant): static
    {
        $this->enfants->removeElement($enfant);

        return $this;
    }
}
