<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Setting
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity
 */
class Setting
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="opt", type="string", length=50, nullable=false)
     */
    private $opt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="data", type="string", length=255, nullable=true)
     */
    private $data;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=30, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="options", type="string", length=255, nullable=true)
     */
    private $options;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOpt(): ?string
    {
        return $this->opt;
    }

    public function setOpt(string $opt): self
    {
        $this->opt = $opt;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(?string $options): self
    {
        $this->options = $options;

        return $this;
    }


}
