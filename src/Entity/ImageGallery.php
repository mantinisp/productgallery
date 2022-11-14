<?php

declare(strict_types=1);

namespace ProductGallery\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class ImageGallery
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_image_gallery", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /** @ORM\Column(name="id_product", type="integer", nullable=true) */
    private ?int $idProduct = null;

    /** @ORM\Column(name="gallery_name", type="string", length=120) */
    private ?string $galleryName = null;

    /** @ORM\Column(name="default_gallery", type="boolean") */
    private bool $defaultGallery = false;

    /** @ORM\Column(name="position", type="integer") */
    private int $position = 0;

    /** @ORM\Column(name="created_at", type="datetime") */
    private ?DateTime $createdAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdImageGallery(): int
    {
        return $this->id;
    }

    public function getIdProduct(): ?int
    {
        return $this->idProduct;
    }

    public function setIdProduct(?int $idProduct): self
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    public function getGalleryName(): ?string
    {
        return $this->galleryName;
    }

    public function setGalleryName(?string $galleryName): self
    {
        $this->galleryName = $galleryName;

        return $this;
    }

    public function isDefaultGallery(): bool
    {
        return $this->defaultGallery;
    }

    public function setDefaultGallery(bool $defaultGallery): self
    {
        $this->defaultGallery = $defaultGallery;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id_image_gallery' => $this->getId(),
            'id_product' => $this->getIdProduct(),
            'gallery_name' => $this->getGalleryName(),
            'default_gallery' => $this->isDefaultGallery(),
            'position' => $this->getPosition(),
            'created_at' => $this->getCreatedAt(),
        ];
    }
}