<?php

namespace App\Entity;

use App\Repository\PostTemplateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostTemplateRepository::class)]
class PostTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\Column(nullable: true)]
    private ?bool $title = null;

    #[ORM\Column(nullable: true)]
    private ?bool $description = null;

    #[ORM\Column(nullable: true)]
    private ?bool $price = null;

    #[ORM\Column(nullable: true)]
    private ?bool $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function isTitle(): ?bool
    {
        return $this->title;
    }

    public function setTitle(bool $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function isDescription(): ?bool
    {
        return $this->description;
    }

    public function setDescription(bool $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isPrice(): ?bool
    {
        return $this->price;
    }

    public function setPrice(bool $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function isImage(): ?bool
    {
        return $this->image;
    }

    public function setImage(bool $image): self
    {
        $this->image = $image;

        return $this;
    }
}
