<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Address;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;

class AddressRepository extends  EntityRepository
{
    public function __construct(EntityManagerInterface $em, ClassMetadata $metaData =null)
    {
        parent::__construct($em, $metaData==null?
            new ClassMetadata(Address::class):$metaData
        );
    }
    public function create(Address $address)
    {
        try {
        $this->_em->persist($address);
        $this->_em->flush($address);
        return  true;
        }catch (OptimisticLockException $e){
            return false;
        }
    }
    public function update(Address $address)
    {
        try {
        $this->_em->merge($address);
        $this->_em->flush($address);
        return  true;
        }catch (OptimisticLockException $e){
            return false;
        }
    }
    public function remove(Address $address)
    {
        try {
            $this->_em->remove($address);
            $this->_em->flush();
            return true;
        }catch (OptimisticLockException  $e){
            return false;
        }
    }
}
