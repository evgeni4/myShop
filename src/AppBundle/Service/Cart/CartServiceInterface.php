<?php


namespace AppBundle\Service\Cart;


use AppBundle\Entity\Cart;
use Symfony\Bundle\SecurityBundle\Tests\Functional\Bundle\AclBundle\Entity\Car;

interface CartServiceInterface
{
    public function addToCart(Cart $cart): bool;

    public function updateCart(Cart $cart): bool;

    public function delete(Cart $cart): bool;

}