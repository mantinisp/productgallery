<?php

declare(strict_types=1);

namespace ProductGallery\Updater;

use ProductGallery\Repository\ProductGalleryRepository;

class ImageGalleryUpdater implements ImageGalleryUpdaterInterface
{
    private ProductGalleryRepository $productGalleryRepository;

    public function __construct(ProductGalleryRepository $productGalleryRepository)
    {
        $this->productGalleryRepository = $productGalleryRepository;
    }

    public function updateImageGalleryByImageId(int $imageId, ?string $galleryId): bool
    {
        if (null === $galleryId || 0 === (int) $galleryId) {
            return $this->productGalleryRepository->findAndUpdateImageGalleryByImage($imageId, null);
        }

        $gallery = $this->productGalleryRepository->findGalleryByGalleryId((int) $galleryId);

        if (!count($gallery)) {
            return false;
        }

        return $this->productGalleryRepository->findAndUpdateImageGalleryByImage($imageId, (int) $galleryId);
    }
}