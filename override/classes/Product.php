<?php

declare(strict_types=1);


class Product extends ProductCore
{
    /**
     * Get product images and legends.
     *
     * @param int $id_lang Language identifier
     * @param Context|null $context
     *
     * @return array Product images and legends
     */
    public function getImages($id_lang, Context $context = null)
    {
        if (Module::isEnabled('productgallery')) {
            return Db::getInstance()->executeS(
                '
                SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
                FROM `' . _DB_PREFIX_ . 'image` i
                ' . Shop::addSqlAssociation('image', 'i') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang . ')
                WHERE i.`id_image_gallery` IS NULL AND i.`id_product` = ' . (int)$this->id . '
                ORDER BY `position`'
            );
        } else {
            return Db::getInstance()->executeS(
                '
                SELECT image_shop.`cover`, i.`id_image`, il.`legend`, i.`position`
                FROM `' . _DB_PREFIX_ . 'image` i
                ' . Shop::addSqlAssociation('image', 'i') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')
                WHERE i.`id_product` = ' . (int) $this->id . '
                ORDER BY `position`'
            );
        }
    }
}