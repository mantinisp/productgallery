<?php

declare(strict_types=1);

/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2016 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registred Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductGallery extends Module
{
    private const INSTALL_SQL_FILE = 'install.sql';

    public const PG_MODULE_NAME = "productgallery";
    public const PG_MODULE_TAB = "administration";
    public const PG_MODULE_VERSION = "1.0.0";
    public const PG_MODULE_AUTHOR = "Mantas";
    public const PG_MODULE_INSTANCE = 0;

    public function __construct()
    {
        $this->name = self::PG_MODULE_NAME;
        $this->tab = self::PG_MODULE_TAB;
        $this->version = self::PG_MODULE_VERSION;
        $this->author = self::PG_MODULE_AUTHOR;
        $this->need_instance = self::PG_MODULE_INSTANCE;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->getTranslator()->trans('PG - Product gallery', [], 'Modules.ProductGallery.Admin');
        $this->description = $this->getTranslator()->trans('Allow users to create multiple product images gallery', [], 'Modules.ProductGallery.Admin');
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function getContent()
    {
        $html = '';

        return $html;
    }

    public function install($keep = true)
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        if (!parent::install()) {
            return false;
        }
        if ($keep) {
            if (!file_exists(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
                return false;
            } elseif (!$sql = file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
                return false;
            }
            $sql = str_replace(['PREFIX_', 'ENGINE_TYPE'], [_DB_PREFIX_, _MYSQL_ENGINE_], $sql);
            $sql = preg_split("/;\s*[\r\n]+/", trim($sql));

            foreach ($sql as $query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }
        if (
            !$this->registerHook('displayAdminProductPageGalleryTab') ||
            !$this->registerHook('actionAdminControllerSetMedia') ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayFooterProduct')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall($keep = true)
    {
        if (
            !parent::uninstall() || ($keep && !$this->deleteTables()) ||
            !$this->unregisterHook('displayAdminProductPageGalleryTab') ||
            !$this->unregisterHook('actionAdminControllerSetMedia') ||
            !$this->unregisterHook('displayHeader') ||
            !$this->unregisterHook('displayFooterProduct')
        ) {
            return false;
        }

        return true;
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }

        return true;
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }

    public function deleteTables()
    {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'image` DROP `id_image_gallery`');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'image_shop` DROP `id_image_gallery`');

        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'image_gallery`'
        );
    }

    public function hookDisplayHeader()
    {
        if ($this->context->controller instanceof ProductControllerCore) {
            $cssUrl = '/modules/' . $this->name . '/views/css/front/productgallery.css';
            $jsUrl = '/modules/' . $this->name . '/views/js/front/productgallery.js';

            $this->context->controller->registerStylesheet(sha1($cssUrl), $cssUrl, ['media' => 'all', 'priority' => 80]);
            $this->context->controller->registerJavascript(sha1($jsUrl), $jsUrl, ['position' => 'bottom', 'priority' => 80]);
        }
    }

    public function hookDisplayFooterProduct($params)
    {
        $idProduct = null;

        if (array_key_exists('product', $params)) {
            $idProduct = $params['product']['id_product'];
        }
        if (!$idProduct) {
            return;
        }
        if (!$this->context->controller instanceof ProductControllerCore) {
            return;
        }

        $productGalleries = $this->context
            ->controller
            ->getContainer()
            ->get(
                'product_gallery.data_provider.product_gallery_data_provider'
            )
            ->getFrontProductGalleriesByProductId(
                $params['product'],
                $this->context->link,
                $this->context->language
            );

        $this->context->smarty->assign(
            [
                'product' => $params['product'],
                'galleries' => $productGalleries,
            ]
        );

        return $this->context
            ->smarty
            ->fetch(
                'module:' . $this->name . '/views/templates/front/gallery.tpl',
            );
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        if ($this->active !== 1) {
            return;
        }
        if (Tools::getValue('controller') == 'AdminProducts') {
            Media::addJsDef(
                [
                    'product_gallery_content_ajax' => $this->getAjaxContent(),
                ]
            );

            $this->context->controller->addCSS(_PS_MODULE_DIR_ . $this->name . '/views/css/admin/productgallery.css');
            $this->context->controller->addJS(_PS_MODULE_DIR_ . $this->name . '/views/js/admin/productgallery.js');
        }
    }

    private function getAjaxContent(): ?string
    {
        global $kernel;
        $requestStack = $kernel->getContainer()->get('request_stack');
        $request = $requestStack->getCurrentRequest();
        $idProduct = $request->get('id');

        if (null === $idProduct) {
            return null;
        }

        return SymfonyContainer::getInstance()
            ->get('router')
            ->generate(
                'admin_product_gallery_index',
                [
                    'id_product' => $idProduct
                ]
            );
    }
}
