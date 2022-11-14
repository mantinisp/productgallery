CREATE TABLE IF NOT EXISTS `PREFIX_image_gallery` (
    `id_image_gallery` int(255) unsigned NOT NULL auto_increment,
    `id_product` int(55) unsigned NULL,
    `gallery_name` varchar(120) NULL,
    `default_gallery` tinyint(1) NOT NULL DEFAULT 0,
    `position` int(20) NOT NULL DEFAULT 0,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id_image_gallery`)
    ) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_image_gallery`
    (id_product, gallery_name, default_gallery, position, created_at)
    VALUES (null, 'default', 1, 1, '2022-11-10 12:00:00');

ALTER TABLE `PREFIX_image_shop` ADD `id_image_gallery` int (255) DEFAULT NULL;
ALTER TABLE `PREFIX_image` ADD `id_image_gallery` int (255) DEFAULT NULL;