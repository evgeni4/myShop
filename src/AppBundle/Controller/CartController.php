<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Form\CartType;
use AppBundle\Service\Cart\CartService;
use AppBundle\Service\Users\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route("/cart/view", name="my_cart")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function cart()
    {
        $title = "My Cart";
        $currentUser = $this->userService->currentUser();
        $carts = $this->cartService->findByUser($currentUser);
        return $this->render('cart/view_cart.html.twig',
            [
                'carts' => $carts,
                'user' => $currentUser,
                'titlePage' => $title,
                'form' => $this->createForm(CartType::class)->createView()
            ]
        );
    }

    /**
     * @Route("/cart/add", name="add_cart",methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response|null
     */
    public function addToCartProcess(Request $request)
    {
        $cart = new Cart();
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);
        var_dump($request->request->all());
        $this->cartService->addToCart($cart);
        return $this->redirectToRoute('my_cart');
    }
}
