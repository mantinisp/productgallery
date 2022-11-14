<?php

declare(strict_types=1);

namespace ProductGallery\Factory;

use ProductGallery\Entity\ImageGallery;
use DateTime;

class ImageGalleryFactory implements ImageGalleryFactoryInterface
{
    public function createFromName(?int $idProduct, ?string $galleryName): ImageGallery
    {
        $gallery = new ImageGallery();
        $gallery->setPosition(0);
        $gallery->setGalleryName($galleryName);
        $gallery->setDefaultGallery(false);
        $gallery->setIdProduct($idProduct);
        $gallery->setCreatedAt(new DateTime());

        return $gallery;
    }
}