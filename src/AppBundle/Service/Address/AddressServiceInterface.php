<?php


namespace AppBundle\Service\Address;


use AppBundle\Entity\Address;

interface AddressServiceInterface
{
    public function insert(Address $address): bool;
    public function update(Address $address): bool;
    public function delete(Address $address): bool;
    public function getOne(int $id): ?Address;
    public function findByAuthor($author);

}