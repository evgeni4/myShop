<?php


namespace AppBundle\Service\Product;


use AppBundle\Entity\Product;

interface ProductServiceInterface
{
    public function insert(Product $product): bool;

    public function update(Product $product): bool;

    public function delete(Product $product): bool;

    public function getOneById(int $id): ?Product;
}