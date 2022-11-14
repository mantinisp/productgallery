<?php

declare(strict_types=1);

namespace ProductGallery\DataProvider;

use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use ProductGallery\Repository\ProductGalleryRepository;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use Language;
use Product;

class ProductGalleryDataProvider implements ProductGalleryDataProviderInterface
{
    private ProductGalleryRepository $productGalleryRepository;

    private ProductDataProvider $productDataProvider;

    private ImageRetriever $imageRetriever;

    public function __construct(ProductGalleryRepository $productGalleryRepository)
    {
        $this->productGalleryRepository = $productGalleryRepository;
        $this->productDataProvider = new ProductDataProvider();
    }

    public function getProductGalleriesByProductId(?string $productId): array
    {
        if (null === $productId) {
            return [];
        }

        return $this->productGalleryRepository->findProductGalleriesByProductId($productId);
    }

    public function getProductGalleriesWithImagesByProductId(?string $productId): array
    {
        if (null === $productId) {
            return [];
        }

        $galleries = $this->productGalleryRepository->findProductGalleriesByProductId($productId);
        $productImagesGalleries = [];

        foreach ($galleries as $gallery) {
            $productImages = $this->getProductImagesByGalleryId($gallery['id_image_gallery']);

            $productImagesGalleries[] = array_merge(
                $gallery,
                [
                    'images' => $productImages
                ]
            );
        }

        return $productImagesGalleries;
    }

    public function getProductImagesByGalleryId(?string $galleryId): array
    {
        if (null === $galleryId) {
            return [];
        }

        $images = $this->productGalleryRepository->findImagesByGalleryId($galleryId);

        $formattedImages = [];

        foreach ($images as $image) {
            $formattedImages[] = $this->productDataProvider->getImage($image['id_image']);
        }

        return $formattedImages;
    }

    public function getFrontProductGalleriesByProductId($product, $link, Language $language): array
    {
        $idProduct = $product['id_product'];
        $productInstance = new Product(
            $product['id_product'],
            false,
            $language->id
        );
        $galleries = $this->productGalleryRepository->findProductGalleriesByProductId((string) $idProduct);
        $this->imageRetriever = new ImageRetriever($link);
        $productImagesGalleries = [];

        foreach ($galleries as $gallery) {
            $productImages = $this->getProductImagesByGalleryId($gallery['id_image_gallery']);
            $images = [];

            foreach ($productImages as $productImage) {
                $image = $this->imageRetriever->getImage(
                    $productInstance,
                    $productImage['id']
                );

                $images[] = $image;
            }

            $productImagesGalleries[] = array_merge(
                $gallery,
                [
                    'images' => $images
                ]
            );
        }

        return $productImagesGalleries;
    }
}