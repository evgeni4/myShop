<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Metals;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\OptimisticLockException;

/**
 * MetalsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MetalsRepository extends \Doctrine\ORM\EntityRepository
{
    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $metaData = null)
    {
        parent::__construct($em, $metaData == null ?
            new Mapping\ClassMetadata(Metals::class) : $metaData
        );
    }

    public function create(Metals $metals)
    {
        try {
            $this->_em->persist($metals);
            $this->_em->flush($metals);
            return true;
        }catch (OptimisticLockException $e){
            return false;
        }
    }
    public function update(Metals $metals)
    {
        try {
            $this->_em->merge($metals);
            $this->_em->flush($metals);
            return true;
        }catch (OptimisticLockException $e){
            return false;
        }
    }
    public function remove(Metals $metals)
    {
        try {
            $this->_em->remove($metals);
            $this->_em->flush($metals);
            return true;
        }catch (OptimisticLockException $e){
            return false;
        }
    }
}
