<?php

declare(strict_types=1);

namespace ProductGallery\DataProvider;

use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider as ProductImageDataProvider;
use Image;

class ProductDataProvider extends ProductImageDataProvider
{
    /**
     * Get an image.
     *
     * @param int $id_image
     *
     * @return array()
     */
    public function getImage($id_image)
    {
        $imageData = new Image((int) $id_image);

        return [
            'id' => $imageData->id,
            'id_product' => $imageData->id_product,
            'position' => $imageData->position,
            'cover' => $imageData->cover ? true : false,
            'legend' => $imageData->legend,
            'format' => $imageData->image_format,
            'base_image_url' => _THEME_PROD_DIR_ . $imageData->getImgPath(),
            'id_image_gallery' => $imageData->id_image_gallery,
        ];
    }
}