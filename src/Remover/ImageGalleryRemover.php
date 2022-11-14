<?php

declare(strict_types=1);

namespace ProductGallery\Remover;

use Doctrine\ORM\EntityManagerInterface;
use ProductGallery\Repository\ProductGalleryRepository;

class ImageGalleryRemover implements ImageGalleryRemoverInterface
{
    private ProductGalleryRepository $productGalleryRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(ProductGalleryRepository $productGalleryRepository, EntityManagerInterface $entityManager)
    {
        $this->productGalleryRepository = $productGalleryRepository;
        $this->entityManager = $entityManager;
    }

    public function removeGallery(int $productId, int $galleryId): bool
    {
        $galleries = $this->productGalleryRepository->findGalleryByProductIdAndGalleryId($productId, $galleryId);

        if (!count($galleries)) {
            return false;
        }

        return $this->productGalleryRepository->findAndRemoveGalleryImagesByProductIdAndGalleryId($productId, $galleryId);
    }
}