<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Address;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Query\Expr\Join;

class UserRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $metaDate = null)
    {
        parent::__construct($em,
            $metaDate == null ?
                new Mapping\ClassMetadata(User::class) :
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

    public function update($user)
    {
        try {
            $this->_em->merge($user);
            $this->_em->flush();
            return true;
        } catch (OptimisticLockException $e) {
            return false;
        }
    }
    public function allCustomers()
    {
        return $this->createQueryBuilder('user')
            ->select(
                'user.id',
                'user.fullName',
                'user.email',
                'address.phone',
                'address.address',
                'address.populated',
                'address.postCode'

            )
            ->join('user.address', 'address')
            ->getQuery()
            ->getResult();
    }
}
