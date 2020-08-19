<?php


namespace AppBundle\Service\Address;


use AppBundle\Entity\Address;
use AppBundle\Repository\AddressRepository;
use AppBundle\Service\Users\UserServiceInterface;

class AddressService implements AddressServiceInterface
{
    private $addressRepository;
    private $author;

    /**
     * AddressService constructor.
     * @param $addressRepository
     * @param $author
     */
    public function __construct(AddressRepository $addressRepository, UserServiceInterface $author)
    {
        $this->addressRepository = $addressRepository;
        $this->author = $author;
    }

    public function insert(Address $address): bool
    {
        $address->setAuthor($this->author->currentUser());
        return $this->addressRepository->create($address);
    }
    public function update(Address $address): bool
    {
       // $address->setAuthor($this->author->currentUser());
        return $this->addressRepository->update($address);
    }
    public function delete(Address $address): bool
    {
       return $this->addressRepository->remove($address);
    }

    public function getOne($id): ?Address
    {
    return $this->addressRepository->find($id);
    }

    public function findByAuthor($author)
    {
        return $this->addressRepository->findBy(['author'=>$author]);
    }
}