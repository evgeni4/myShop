<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Form\CartType;
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

    public function __construct(CartService $cartService, UserService $userService)
    {
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
        $carts = $this->cartService->findByCartStatus($currentUser->getId());
        $cartStatus = $this->cartService->findByCartStatus($currentUser->getId());

        return $this->render('cart/view_cart.html.twig',
            [
                'cartStatus' => $cartStatus,
                'carts' => $carts,
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
        $currentUser=$this->userService->currentUser();
        $data = $request->request->get('cart');
        $cartCheck = $this->cartService->checkOneCart(intval($data['productId']),$currentUser->getId());
        if ($cartCheck){
            $this->addFlash('info', 'This product has already been added!');
            return $this->redirectToRoute('shop_index');
        }
        $form->handleRequest($request);
        $this->cartService->addToCart($cart);
        return $this->redirectToRoute('edit_cart');
    }

    /**
     * @Route("/cart/quentity/edit",name="edit_quantity")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return RedirectResponse
     */
    public function editCartProcess(Request $request)
    {
        $data = $request->request->get('cart');
        $id = intval($data['id']);
        $cart = $this->cartService->getOneCart($id);
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);
        $this->cartService->updateCart($cart);
        $this->addFlash('info', 'Quantity changed successfully!');
        return $this->redirectToRoute('edit_cart');
    }

    /**
     * @Route("/cart/delete/{id}", name="delete_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return Response|null
     */
    public function deleteCartProcess(Request $request, int $id)
    {
        $cart = $this->cartService->getOneCart($id);
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);
        if (null === $cart) {
            return $this->redirectToRoute('edit_cart');
        }
        $this->cartService->delete($cart);
        $this->addFlash('info', 'Delete product successfully!');
        return $this->redirectToRoute('edit_cart');
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
        foreach ($carts as $cart) {
            $cart->setStatus(1);
            $this->cartService->updateCartStatus($cart);
        }
        $this->addFlash('info', 'Thank you! Your order has been successfully confirmed.!');
        return $this->redirectToRoute('all_orders');
    }
}
