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

        $newOrderNumber = $this->orderGenerate();
        $cart->setOrderNumber($newOrderNumber);
        $cart->setUserId($this->customer->currentUser());
        $sum = $cart->getQuantity() * $cart->getProductId()->getPrice();
        $cart->setTotalSum($sum);
        $cart->setStatus(0);
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

    public function checkOneCart(int $id, int $userId)
    {
        return $this->cartRepository->findBy(['productId' => $id, 'userId' => $userId, 'status' => 0]);
    }

    public function updateCartStatus(Cart $cart): bool
    {
        return $this->cartRepository->update($cart);
    }

    public function findByUser($id)
    {
        return $this->cartRepository->findBy(['userId' => $id, 'status' => '1']);
    }

    public function findByCartStatus()
    {
        $userId = is_object($this->customer->currentUser()?$this->customer->currentUser()->getId():"");
        if ($userId!==null){
            $userId = $this->customer->currentUser()->getId();
            return $this->cartRepository->findBy(['userId' => $userId, 'status' => '0']);
        }

    }

    public function userOrdersCompletes()
    {

        return $this->cartRepository->findBy(['status' => '1'],['userId'=>'asc']);
    }

    public function userOrdersPending()
    {
        return $this->cartRepository->findBy(['status' => '0']);
    }

    /**
     * @return int|string
     */
    private function orderGenerate()
    {
        $lastId = $this->cartRepository->findAll();
        $lastId = $lastId[count($lastId) - 1];
        $newOrderNumber = $lastId->getId()+1;
        switch (strlen($newOrderNumber)) {
            case 1:
                $newOrderNumber = "0000" . $newOrderNumber;
                break;
            case 2:
                $newOrderNumber = "000" . $newOrderNumber;
                break;
            case 3:
                $newOrderNumber = "00" . $newOrderNumber;
                break;
        }
        return $newOrderNumber;
    }
}