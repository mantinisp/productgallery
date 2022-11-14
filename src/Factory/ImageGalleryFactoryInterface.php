<?php

declare(strict_types=1);

namespace ProductGallery\Factory;

use ProductGallery\Entity\ImageGallery;

interface ImageGalleryFactoryInterface
{
    public function createFromName(?int $idProduct, ?string $galleryName): ImageGallery;
}