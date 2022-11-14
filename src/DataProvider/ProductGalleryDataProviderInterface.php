<?php

declare(strict_types=1);

namespace ProductGallery\DataProvider;

use Language;

interface ProductGalleryDataProviderInterface
{
    public function getProductGalleriesByProductId(?string $productId): array;

    public function getProductGalleriesWithImagesByProductId(?string $productId): array;

    public function getProductImagesByGalleryId(?string $galleryId): array;

    public function getFrontProductGalleriesByProductId($product, $link, Language $language): array;
}