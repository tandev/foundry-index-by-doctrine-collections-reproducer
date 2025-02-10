<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: Translation::class, mappedBy: 'category', cascade: ['persist'], indexBy: 'culture')]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $translations;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Space $space = null;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTranslations(): array
    {
        return $this->translations->toArray();
    }

    public function addTranslation(Translation $translation): void
    {
        $this->translations[$translation->getCulture()] = $translation;
    }

    public function removeTranslation(Translation $translation): void
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
        }
    }

    public function getTranslation(string $culture = 'en'): ?Translation
    {
        return $this->translations->get($culture);
    }

    public function getSpace(): ?Space
    {
        return $this->space;
    }

    public function setSpace(?Space $space): static
    {
        $this->space = $space;

        return $this;
    }
}
