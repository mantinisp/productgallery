<?php

declare(strict_types=1);


class Image extends ImageCore
{
    /** @var int Image gallery ID */
    public $id_image_gallery;
    public function __construct($id = null, $idLang = null)
    {
        self::$definition['fields']['id_image_gallery'] = ['type' => self::TYPE_INT, 'allow_null' => true, 'shop' => 'both'];
        parent::__construct($id, $idLang);
    }
}