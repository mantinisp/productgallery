<?php

declare(strict_types=1);

namespace ProductGallery\Controller\Admin;

use ProductGallery\DataProvider\ProductGalleryDataProviderInterface;
use ProductGallery\Updater\ImageGalleryUpdaterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Controller\Admin\ProductImageController as ImageController;

class ProductImageController extends ImageController
{
    private ProductGalleryDataProviderInterface $productGalleryDataProvider;

    private ImageGalleryUpdaterInterface $imageGalleryUpdater;

    public function __construct(ProductGalleryDataProviderInterface $productGalleryDataProvider, ImageGalleryUpdaterInterface $imageGalleryUpdater)
    {
        parent::__construct();
        $this->productGalleryDataProvider = $productGalleryDataProvider;
        $this->imageGalleryUpdater = $imageGalleryUpdater;
    }

    /**
     * Manage form image.
     *
     * @Template("@Modules/productgallery/views/templates/admin/ProductImage/form.html.twig")
     *
     * @param string|int $idImage
     * @param Request $request
     *
     * @return array|JsonResponse|Response
     */
    public function formAction($idImage, Request $request)
    {
        $locales = $this->get('prestashop.adapter.legacy.context')->getLanguages();
        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');

        if ($idImage == 0 || !$request->isXmlHttpRequest()) {
            return new Response();
        }

        $image = $productAdapter->getImage((int) $idImage);
        $galleriesList = $this->productGalleryDataProvider->getProductGalleriesByProductId($image['id_product']);

        $choices = [];
        $choices['default'] = 0;

        foreach ($galleriesList as $galleryList) {
            $choices[$galleryList['gallery_name']] = $galleryList['id_image_gallery'];
        }

        $form = $this->get('form.factory')->createNamedBuilder('form_image', FormType::class, $image, ['csrf_protection' => false])
            ->add('legend', 'PrestaShopBundle\Form\Admin\Type\TranslateType', [
                'type' => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
                'options' => [],
                'locales' => $locales,
                'hideTabs' => true,
                'label' => $this->trans('Caption', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('cover', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                'label' => $this->trans('Cover image', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('id_image_gallery', ChoiceType::class, [
                'choices' => $choices,
                'attr' => [
                    'class' => 'custom-select',
                ],
                'required' => false,
                'label' => $this->trans('Galleries list', 'Admin.Catalog.Feature'),
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $jsonResponse = new JsonResponse();

            if ($form->isValid()) {
                $jsonResponse->setData($adminProductWrapper->ajaxProcessUpdateImage($idImage, $form->getData()));
            } else {
                $error_msg = [];
                foreach ($form->getErrors() as $error) {
                    $error_msg[] = $error->getMessage();
                }

                $jsonResponse->setData(['message' => implode(' ', $error_msg)]);
                $jsonResponse->setStatusCode(400);
            }

            return $jsonResponse;
        }

        return [
            'image' => $image,
            'form' => $form->createView(),
        ];
    }

    public function updateImageGalleryAction(Request $request)
    {
        $imageId = $request->get('id_image');
        $galleryId = $request->get('id_gallery');
        $fromTab = (bool) $request->get('tab');
        $productAdapter = $this->get('prestashop.adapter.data_provider.product');
        $jsonResponse = new JsonResponse();

        if ($imageId === 0) {
            return $jsonResponse;
        }

        $image = $productAdapter->getImage((int) $imageId);

        if ($image) {
            $this->imageGalleryUpdater->updateImageGalleryByImageId($image['id'], $galleryId);
        }

        if ($fromTab) {
            return $this->renderTabView($image);
        }

        return $jsonResponse->setData(['image' => $image]);
    }

    public function renderTabView(array $image): Response
    {
        $productGalleriesWithImages = $this->productGalleryDataProvider->getProductGalleriesWithImagesByProductId($image['id_product']);
        $availableGalleries = $this->productGalleryDataProvider->getProductGalleriesByProductId($image['id_product']);

        return $this->render('@Modules/productgallery/views/templates/admin/gallery.html.twig', [
            'id_product' => $image['id_product'],
            'galleries' => $productGalleriesWithImages,
            'available_galleries' => $availableGalleries,
        ]);
    }
}