<?php

namespace App\Entity;

use App\Repository\PageSectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PageSectionRepository::class)
 */
class PageSection {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="integer")
     */
    private $rank;

    /**
     * @ORM\ManyToOne(targetEntity=Page::class, inversedBy="pageSections")
     */
    private $page;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $alignContent;

    /**
     * @ORM\Column(type="text")
     */
    private $alignTitle;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $headerIcon;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $headerIconColor;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $backgroundContainment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $contentContainment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $backgroundColor;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $textColor;

    /**
     * @ORM\Column(type="blob",nullable=true)
     */
    private $imageData;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageFileType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageFileName;

    /**
     * @ORM\OneToMany(targetEntity=PageSectionImages::class, mappedBy="pageSection")
     */
    private $pageSectionImages;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $youtubeUrls;

    public function __construct()
    {
        $this->pageSectionImages = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function setTitle(string $title): self {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function setContent(string $content): self {
        $this->content = $content;

        return $this;
    }

    public function getRank(): ?int {
        return $this->rank;
    }

    public function setRank(int $rank): self {
        $this->rank = $rank;

        return $this;
    }

    public function getPage(): ?Page {
        return $this->page;
    }

    public function setPage(?Page $page): self {
        $this->page = $page;

        return $this;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function setType(string $type): self {
        $this->type = $type;

        return $this;
    }

    public function getAlignContent(): ?string {
        return $this->alignContent;
    }

    public function setAlignContent(string $alignContent): self {
        $this->alignContent = $alignContent;

        return $this;
    }

    public function getAlignTitle(): ?string {
        return $this->alignTitle;
    }

    public function setAlignTitle(string $alignTitle): self {
        $this->alignTitle = $alignTitle;

        return $this;
    }

    public function getHeaderIcon(): ?string {
        return $this->headerIcon;
    }

    public function setHeaderIcon(?string $headerIcon): self {
        $this->headerIcon = $headerIcon;

        return $this;
    }

    public function getHeaderIconColor(): ?string {
        return $this->headerIconColor;
    }

    public function setHeaderIconColor(string $headerIconColor): self {
        $this->headerIconColor = $headerIconColor;

        return $this;
    }

    public function getBackgroundContainment(): ?string {
        return $this->backgroundContainment;
    }

    public function setBackgroundContainment(string $backgroundContainment): self {
        $this->backgroundContainment = $backgroundContainment;

        return $this;
    }

    public function getContentContainment(): ?string {
        return $this->contentContainment;
    }

    public function setContentContainment(string $contentContainment): self {
        $this->contentContainment = $contentContainment;

        return $this;
    }

    public function getBackgroundColor(): ?string {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(string $backgroundColor): self {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getTextColor(): ?string {
        return $this->textColor;
    }

    public function setTextColor(string $textColor): self {
        $this->textColor = $textColor;

        return $this;
    }

    public function getImageData()
    {
        return $this->imageData;
    }

    public function setImageData($imageData): self
    {
        $this->imageData = $imageData;

        return $this;
    }

    public function getImageFileType(): ?string
    {
        return $this->imageFileType;
    }

    public function setImageFileType(?string $imageFileType): self
    {
        $this->imageFileType = $imageFileType;

        return $this;
    }

    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(?string $imageFileName): self
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    /**
     * @return Collection|PageSectionImages[]
     */
    public function getPageSectionImages(): Collection
    {
        return $this->pageSectionImages;
    }

    public function addPageSectionImage(PageSectionImages $pageSectionImage): self
    {
        if (!$this->pageSectionImages->contains($pageSectionImage)) {
            $this->pageSectionImages[] = $pageSectionImage;
            $pageSectionImage->setPageSection($this);
        }

        return $this;
    }

    public function removePageSectionImage(PageSectionImages $pageSectionImage): self
    {
        if ($this->pageSectionImages->contains($pageSectionImage)) {
            $this->pageSectionImages->removeElement($pageSectionImage);
            // set the owning side to null (unless already changed)
            if ($pageSectionImage->getPageSection() === $this) {
                $pageSectionImage->setPageSection(null);
            }
        }

        return $this;
    }

    public function getYoutubeUrls(): ?string
    {
        return $this->youtubeUrls;
    }

    public function setYoutubeUrls(?string $youtubeUrls): self
    {
        $this->youtubeUrls = $youtubeUrls;

        return $this;
    }

}
