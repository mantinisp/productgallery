<?php

declare(strict_types=1);

namespace ProductGallery\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Adapter\Entity\Product;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use ProductGallery\DataProvider\ProductGalleryDataProviderInterface;
use ProductGallery\Factory\ImageGalleryFactoryInterface;
use ProductGallery\Remover\ImageGalleryRemoverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductGalleryController extends FrameworkBundleAdminController
{
    private ProductGalleryDataProviderInterface $productGalleryDataProvider;

    private ImageGalleryFactoryInterface $imageGalleryFactory;

    private ImageGalleryRemoverInterface $imageGalleryRemover;

    private ProductDataProvider $productDataProvider;

    private EntityManagerInterface $entityManager;

    public function __construct(
        ProductGalleryDataProviderInterface $productGalleryDataProvider,
        ImageGalleryFactoryInterface $imageGalleryFactory,
        ImageGalleryRemoverInterface $imageGalleryRemover,
        ProductDataProvider $productDataProvider,
        EntityManagerInterface $entityManager
    ){
        parent::__construct();
        $this->productGalleryDataProvider = $productGalleryDataProvider;
        $this->imageGalleryFactory = $imageGalleryFactory;
        $this->imageGalleryRemover = $imageGalleryRemover;
        $this->productDataProvider = $productDataProvider;
        $this->entityManager = $entityManager;
    }

    public function renderAction(Request $request): Response
    {
        return $this->renderTabView($request->get('id_product'));
    }

    public function createGalleryAction(Request $request): Response
    {
        $idProduct = $request->get('id_product');
        $galleryName = $request->get('inputValue');

        if (!$galleryName) {
            return $this->renderTabView($idProduct);
        }

        /** @var Product $product */
        $product = $this->productDataProvider->getProduct((int) $idProduct);

        if (!$product) {
            return $this->renderTabView($idProduct);
        }

        $galleryFactory = $this->imageGalleryFactory->createFromName((int) $idProduct, $galleryName);

        $this->entityManager->persist($galleryFactory);
        $this->entityManager->flush();

        return $this->renderTabView($idProduct);
    }

    public function deleteGalleryAction(Request $request): Response
    {
        $idProduct = $request->get('id_product');
        $idGallery = $request->get('id_gallery');

        if (!$idGallery) {
            return $this->renderTabView($idProduct);
        }

        $this->imageGalleryRemover->removeGallery((int) $idProduct, (int) $idGallery);

        return $this->renderTabView($idProduct);
    }

    private function renderTabView($idProduct): Response
    {
        $productGalleriesWithImages = $this->productGalleryDataProvider->getProductGalleriesWithImagesByProductId($idProduct);
        $availableGalleries = $this->productGalleryDataProvider->getProductGalleriesByProductId($idProduct);

        return $this->render('@Modules/productgallery/views/templates/admin/gallery.html.twig', [
            'id_product' => $idProduct,
            'galleries' => $productGalleriesWithImages,
            'available_galleries' => $availableGalleries,
        ]);
    }
}