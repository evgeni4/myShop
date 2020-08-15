<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\OptimisticLockException;

class UserRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $metaDate = null)
    {
        parent::__construct($em,
            $metaDate == null ?
                new Mapping\ClassMetadata(User::class):
                $metaDate
        );
    }
    public function insert(User $user)
    {
        try {
            $this->_em->persist($user);
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }
}
