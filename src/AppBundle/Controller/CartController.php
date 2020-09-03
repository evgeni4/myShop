<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Form\CartType;
use AppBundle\Service\Address\AddressService;
use AppBundle\Service\Cart\CartService;
use AppBundle\Service\Users\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends Controller
{
    private $cartService;
    private $userService;
    private $addressService;

    public function __construct(AddressService $addressService,CartService $cartService, UserService $userService)
    {
        $this->addressService = $addressService;
        $this->cartService = $cartService;
        $this->userService = $userService;
    }

    /**
     * @Route("/cart/view", name="edit_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function cart()
    {
        $title = "My Cart";
        $currentUser = $this->userService->currentUser();
        $addressUser = $this->addressService->findByAuthor($currentUser->getId());
        return $this->render('cart/view_cart.html.twig',
            [
                'addressUser' => $addressUser,
                'user' => $currentUser,
                'titlePage' => $title,
                'form' => $this->createForm(CartType::class)->createView()
            ]
        );
    }


    /**
     * @Route("/cart/add", name="add_cart" ,methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response|null
     */
    public function addToCartProcess(Request $request)
    {
        $cart = new Cart();
        $form = $this->createForm(CartType::class, $cart);
        $currentUser = $this->userService->currentUser();

        $data = $request->request->get('cart');

        $cartCheck = $this->cartService->checkOneCart(intval($data['productId']), $currentUser->getId());
        if ($currentUser->isSales() || $currentUser->isAdmin()) {
            $this->addFlash('info', 'You cannot shop here!');
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        if ($cartCheck) {
            $this->addFlash('info', 'This product has already been added!');
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        $form->handleRequest($request);
        $this->cartService->addToCart($cart);
        $this->addFlash('successfully', 'Product added to cart successfully!');
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/cart/quantity/edit",name="edit_quantity", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return RedirectResponse
     */
    public function editCartQuantityProcess(Request $request)
    {
        $data = $request->request->get('cart');
        $id = intval($data['id']);
        $cart = $this->cartService->getOneCart($id);
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);
        $this->cartService->updateCart($cart);
        $this->addFlash('successfully', 'Quantity changed successfully!');
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/cart/delete", name="delete_cart", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response|null
     */
    public function deleteCartProcess(Request $request)
    {
        $data = $request->request->get('cart');
        $id = intval($data['id']);
        $cart = $this->cartService->getOneCart($id);
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);
        if (null === $cart) {
           return $this->redirectToRoute('shop_index');
        }
        if ($form->isSubmitted()){
            $this->cartService->delete($cart);
            $this->addFlash('successfully', 'Delete product successfully!');
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * @return RedirectResponse
     * @Route("/cart/confirm", name="confirm_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function confirmProcess()
    {
        $currentUser = $this->userService->currentUser();
        $carts = $this->cartService->findByCartStatus($currentUser->getId());
        $addressUser = $this->addressService->findByAuthor($currentUser->getId());
        if (!$addressUser){
            return  $this->redirectToRoute('add_address');
        }
        foreach ($carts as $cart) {
            $cart->setStatus(1);
            $this->cartService->updateCartStatus($cart);
        }
        $this->addFlash('info', 'Thank you! Your order has been successfully confirmed.!');
        return $this->redirectToRoute('all_orders');
    }
}
