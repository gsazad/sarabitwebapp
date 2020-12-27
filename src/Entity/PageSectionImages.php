<?php

namespace App\Entity;

use App\Repository\PageSectionImagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PageSectionImagesRepository::class)
 */
class PageSectionImages
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="blob")
     */
    private $fileData;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileType;

    /**
     * @ORM\ManyToOne(targetEntity=PageSection::class, inversedBy="pageSectionImages")
     */
    private $pageSection;

    /**
     * @ORM\Column(type="integer")
     */
    private $rank;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $showTitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $showDescription;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $titleColor;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $descriptionColor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileData()
    {
        return $this->fileData;
    }

    public function setFileData($fileData): self
    {
        $this->fileData = $fileData;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    public function setFileType(string $fileType): self
    {
        $this->fileType = $fileType;

        return $this;
    }

    public function getPageSection(): ?PageSection
    {
        return $this->pageSection;
    }

    public function setPageSection(?PageSection $pageSection): self
    {
        $this->pageSection = $pageSection;

        return $this;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(int $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getShowTitle(): ?string
    {
        return $this->showTitle;
    }

    public function setShowTitle(?string $showTitle): self
    {
        $this->showTitle = $showTitle;

        return $this;
    }

    public function getShowDescription(): ?string
    {
        return $this->showDescription;
    }

    public function setShowDescription(?string $showDescription): self
    {
        $this->showDescription = $showDescription;

        return $this;
    }

    public function getTitleColor(): ?string
    {
        return $this->titleColor;
    }

    public function setTitleColor(?string $titleColor): self
    {
        $this->titleColor = $titleColor;

        return $this;
    }

    public function getDescriptionColor(): ?string
    {
        return $this->descriptionColor;
    }

    public function setDescriptionColor(?string $descriptionColor): self
    {
        $this->descriptionColor = $descriptionColor;

        return $this;
    }
}
