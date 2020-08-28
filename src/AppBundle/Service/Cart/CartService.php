<?php


namespace AppBundle\Service\Cart;


use AppBundle\Entity\Cart;
use AppBundle\Repository\CartRepository;
use AppBundle\Service\Users\UserServiceInterface;

class CartService implements CartServiceInterface
{
    private $cartRepository;
    private $customer;

    /**
     * CartService constructor.
     * @param $cartRepository
     * @param $customer
     */
    public function __construct(CartRepository $cartRepository, UserServiceInterface $customer)
    {
        $this->cartRepository = $cartRepository;
        $this->customer = $customer;
    }

    public function addToCart(Cart $cart): bool
    {
        $cart->setUserId($this->customer->currentUser());
        $sum = $cart->getQuantity() * $cart->getProductId()->getPrice();
        $cart->setTotalSum($sum);
        $cart->setStatus(0);
//        var_dump($cart);
//        exit;
        return $this->cartRepository->create($cart);
    }

    public function updateCart(Cart $cart): bool
    {
        $sum = $cart->getQuantity() * $cart->getProductId()->getPrice();
        $cart->setTotalSum($sum);
        return $this->cartRepository->update($cart);
    }

    public function delete(Cart $cart): bool
    {
        return $this->cartRepository->remove($cart);
    }

    public function getOneCart(int $id): ?Cart
    {
        return $this->cartRepository->find($id);
    }

    public function updateCartStatus(Cart $cart): bool
    {
        return $this->cartRepository->update($cart);
    }

    public function findByUser($id)
    {
        return $this->cartRepository->findBy(['userId'=>$id,'status'=>'1']);
    }
    public function findByCartStatus($id)
    {
        return $this->cartRepository->findBy(['userId'=>$id,'status'=>'0']);
    }

    public function userOrdersCompletes()
    {
        return $this->cartRepository->findBy(['status'=>'1']);
    }
    public function userOrdersPending()
    {
        return $this->cartRepository->findBy(['status'=>'0']);
    }
}