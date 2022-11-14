<?php

declare(strict_types=1);

namespace ProductGallery\Remover;

interface ImageGalleryRemoverInterface
{
    public function removeGallery(int $productId, int $galleryId): bool;
}