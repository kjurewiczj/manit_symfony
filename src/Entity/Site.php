<?php

namespace App\Entity;

use App\Interfaces\Entity\CreatedAtInterface;
use App\Repository\SiteRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
        operations: [
            new Get(normalizationContext: ['groups' => ['getSite']]),
        ],
    paginationEnabled: false,
)]
#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site implements CreatedAtInterface, \ArrayAccess
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups('getSite')]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups('getSite')]
    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'site', targetEntity: UserSite::class)]
    private Collection $userSites;

    #[Groups('getSite')]
    #[ORM\Column(nullable: true)]
    private ?string $metaTitle;

    #[Groups('getSite')]
    #[ORM\Column(nullable: true)]
    private ?string $metaDescription;

    #[Groups('getSite')]
    #[ORM\Column(nullable: true)]
    private ?string $ogTitle;

    #[Groups('getSite')]
    #[ORM\Column(nullable: true)]
    private ?string $ogDescription;

    #[Groups('getSite')]
    #[ORM\Column(nullable: true)]
    private ?string $ogImage;

    #[Groups('getSite')]
    #[ORM\Column(nullable: true)]
    private ?string $twitterTitle;

    #[Groups('getSite')]
    #[ORM\Column(nullable: true)]
    private ?string $twitterDescription;

    #[Groups('getSite')]
    #[ORM\Column(nullable: true)]
    private ?string $twitterImage;

    #[ORM\Column(nullable: true)]
    private ?string $serverIp;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function offsetGet($offset): mixed
    {
        return $this->$offset;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->$offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->$offset);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function getUserSites(): Collection
    {
        return $this->userSites;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getOgTitle(): ?string
    {
        return $this->ogTitle;
    }

    public function setOgTitle(?string $ogTitle): void
    {
        $this->ogTitle = $ogTitle;
    }

    public function getOgDescription(): ?string
    {
        return $this->ogDescription;
    }

    public function setOgDescription(?string $ogDescription): void
    {
        $this->ogDescription = $ogDescription;
    }

    public function getOgImage(): ?string
    {
        return $this->ogImage;
    }

    public function setOgImage(?string $ogImage): void
    {
        $this->ogImage = $ogImage;
    }

    public function getTwitterTitle(): ?string
    {
        return $this->twitterTitle;
    }

    public function setTwitterTitle(?string $twitterTitle): void
    {
        $this->twitterTitle = $twitterTitle;
    }

    public function getTwitterDescription(): ?string
    {
        return $this->twitterDescription;
    }

    public function setTwitterDescription(?string $twitterDescription): void
    {
        $this->twitterDescription = $twitterDescription;
    }

    public function getTwitterImage(): ?string
    {
        return $this->twitterImage;
    }

    public function setTwitterImage(?string $twitterImage): void
    {
        $this->twitterImage = $twitterImage;
    }

    public function getServerIp(): ?string
    {
        return $this->serverIp;
    }

    public function setServerIp(?string $serverIp): void
    {
        $this->serverIp = $serverIp;
    }
}
