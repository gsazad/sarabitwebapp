<?php

namespace App\Entity;

use App\Repository\GfontRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GfontRepository::class)
 */
class Gfont
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $familyName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity=PageSection::class, mappedBy="titleFont")
     */
    private $pageSections;

    public function __construct()
    {
        $this->pageSections = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName): self
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection|PageSection[]
     */
    public function getPageSections(): Collection
    {
        return $this->pageSections;
    }

    public function addPageSection(PageSection $pageSection): self
    {
        if (!$this->pageSections->contains($pageSection)) {
            $this->pageSections[] = $pageSection;
            $pageSection->setTitleFont($this);
        }

        return $this;
    }

    public function removePageSection(PageSection $pageSection): self
    {
        if ($this->pageSections->contains($pageSection)) {
            $this->pageSections->removeElement($pageSection);
            // set the owning side to null (unless already changed)
            if ($pageSection->getTitleFont() === $this) {
                $pageSection->setTitleFont(null);
            }
        }

        return $this;
    }

    
}
