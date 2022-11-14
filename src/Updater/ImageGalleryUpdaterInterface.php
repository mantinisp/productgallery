<?php

declare(strict_types=1);

namespace ProductGallery\Updater;

interface ImageGalleryUpdaterInterface
{
    public function updateImageGalleryByImageId(int $imageId, ?string $galleryId): bool;
}