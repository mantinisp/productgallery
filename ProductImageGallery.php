<?php

declare(strict_types=1);


class ProductImageGallery extends ObjectModel
{
    /** @var int */
    public $id;

    /** @var int */
    public $id_product;

    /** @var string */
    public $gallery_name;

    /** @var bool */
    public $default_gallery;

    /** @var int */
    public $position;

    /** @var string */
    public $created_at;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'image_gallery',
        'primary' => 'id_image_gallery',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => false],
            'gallery_name' => ['type' => self::TYPE_STRING],
            'default_gallery' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'position' => ['type' => self::TYPE_INT],
            'created_at' => ['type' => self::TYPE_DATE],
        ],
    ];

    public static function getGalleryByProductId($id_product)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'image_gallery` ig WHERE ig.`id_product` = ' . (int) $id_product . '';

        return Db::getInstance((bool) _PS_USE_SQL_SLAVE_)->getRow($sql);
    }
}