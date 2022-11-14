<?php

declare(strict_types=1);

namespace ProductGallery\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use PrestaShopBundle\Entity\Repository\NormalizeFieldTrait;
use PrestaShopBundle\Exception\NotImplementedException;
use RuntimeException;
use Shop;

class ProductGalleryRepository
{
    use NormalizeFieldTrait;

    private Connection $connection;

    private ?string $tablePrefix;

    private LegacyContext $contextAdapter;

    private int $shopId;

    /**
     * @throws NotImplementedException
     */
    public function __construct(
        Connection $connection,
        ContextAdapter $contextAdapter,
        $tablePrefix
    ) {
        $this->connection = $connection;
        $this->tablePrefix = $tablePrefix;

        $this->contextAdapter = $contextAdapter;
        $context = $contextAdapter->getContext();

        if (!$context->shop instanceof Shop) {
            throw new RuntimeException('Determining the active shop requires a contextual shop instance.');
        }

        $shop = $context->shop;

        if ($shop->getContextType() !== $shop::CONTEXT_SHOP) {
            throw new NotImplementedException('Shop context types other than "single shop" are not supported');
        }

        $this->shopId = $shop->getContextualShopId();
    }

    public function findProductGalleriesByProductId(string $productId): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from($this->tablePrefix . 'image_gallery', 'ig')
            ->where('ig.id_product = :id_product')
            ->setParameter('id_product', $productId)
            ->orderBy('ig.created_at', 'DESC')
        ;

        return $qb->execute()->fetchAll();
    }

    public function findGalleryByGalleryId(int $galleryId)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from($this->tablePrefix . 'image_gallery', 'ig')
            ->where('ig.id_image_gallery = :id_image_gallery')
            ->setParameter('id_image_gallery', $galleryId)
        ;

        return $qb->execute()->fetchAll();
    }

    public function findImagesByGalleryId(string $galleryId): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from($this->tablePrefix . 'image', 'pi')
            ->where('pi.id_image_gallery = :id_image_gallery')
            ->setParameter('id_image_gallery', $galleryId)
            ->orderBy('pi.position')
        ;

        return $qb->execute()->fetchAll();
    }

    public function findGalleryByProductIdAndGalleryId(int $productId, int $galleryId): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from($this->tablePrefix . 'image_gallery', 'ig')
            ->where('ig.id_image_gallery = :id_image_gallery')
            ->andWhere('ig.id_product = :id_product')
            ->setParameter('id_image_gallery', $galleryId)
            ->setParameter('id_product', $productId)
            ->orderBy('ig.position')
        ;

        return $qb->execute()->fetchAll();
    }

    public function findAndRemoveGalleryImagesByProductIdAndGalleryId(int $productId, int $galleryId): bool
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->delete($this->tablePrefix . 'image')
            ->where('id_product = :id_product')
            ->andWhere('id_image_gallery = :id_image_gallery')
            ->setParameter('id_product', $productId)
            ->setParameter('id_image_gallery', $galleryId)
        ;
        $qb->execute();

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->delete($this->tablePrefix . 'image_gallery')
            ->where('id_product = :id_product')
            ->andWhere('id_image_gallery = :id_image_gallery')
            ->setParameter('id_product', $productId)
            ->setParameter('id_image_gallery', $galleryId)
        ;
        $qb->execute();

        return true;
    }

    public function findAndUpdateImageGalleryByImage(int $imageId, ?int $galleryId): bool
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->update($this->tablePrefix . 'image', 'img')
            ->set('img.id_image_gallery',':id_image_gallery')
            ->where('img.id_image = :id_image')
            ->setParameter('id_image_gallery', $galleryId)
            ->setParameter('id_image', $imageId)
        ;
        $qb->execute();

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->update($this->tablePrefix . 'image_shop', 'img')
            ->set('img.id_image_gallery',':id_image_gallery')
            ->where('img.id_image = :id_image')
            ->setParameter('id_image_gallery', $galleryId)
            ->setParameter('id_image', $imageId)
        ;
        $qb->execute();

        return true;
    }
}