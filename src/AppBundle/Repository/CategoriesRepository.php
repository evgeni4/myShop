<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\OptimisticLockException;

/**
 * CategoriesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoriesRepository extends \Doctrine\ORM\EntityRepository
{
    public function __construct(
        EntityManagerInterface $em,
        ClassMetadata $metaData = null
    )
    {
        parent::__construct($em,
            $metaData == null ?
                new  ClassMetadata(Categories::class) : $metaData
        );
    }

    public function create(Categories $categories)
    {
        try {
            $this->_em->persist($categories);
            $this->_em->flush($categories);
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }
    public function update(Categories $categories)
    {
        try {
            $this->_em->merge($categories);
            $this->_em->flush($categories);
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    } public function remove(Categories $categories)
    {
        try {
            $this->_em->remove($categories);
            $this->_em->flush($categories);
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }
}
