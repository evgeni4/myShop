<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\OptimisticLockException;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends \Doctrine\ORM\EntityRepository
{
    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $metaData = null)
    {
        parent::__construct($em, $metaData == null ?
            new Mapping\ClassMetadata(Product::class) : $metaData
        );
    }

    public function create(Product $product)
    {
        try {
            $this->_em->persist($product);
            $this->_em->flush($product);
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }

    public function update(Product $product)
    {
        try {
            $this->_em->merge($product);
            $this->_em->flush($product);
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }

    public function remove(Product $product)
    {
        try {
            $this->_em->remove($product);
            $this->_em->flush($product);
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }

    public function collectionNew()
    {
        return $this->createQueryBuilder('product')
            ->andWhere('product.status = 1')
            ->orderBy('product.dateAdded', 'DESC')
            ->orderBy('product.discount','DESC')
            ->getQuery()
            ->execute();
    }

    public function productsByCategory($id)
    {
        return $this->createQueryBuilder('product')
            ->where("product.category = $id")
            ->orderBy('product.dateAdded', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function newProducts()
    {
        return $this->createQueryBuilder('product')
            ->orderBy('product.dateAdded', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->execute();
    }

    public function searchProcess( $data)
    {
        return $this->createQueryBuilder('product')
            ->where( 'product.title LIKE :word')
            ->orWhere('product.price LIKE :word')
            ->orWhere('product.oldPrice LIKE :word')
            ->orWhere('product.discount LIKE :word')
            ->orWhere('product.gender LIKE :word')
            ->setParameter('word', '%'.$data.'%')
            ->orderBy('product.dateAdded', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
