<?php

namespace AppBundle\Controller;

use AppBundle\Service\Cart\CartService;
use AppBundle\Service\Categories\CategoriesService;
use AppBundle\Service\Product\ProductService;
use AppBundle\Service\Users\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    private $userService;
    private $categoriesService;
    private $cartService;
    private $productService;
    public function __construct(ProductService $productService,CartService $cartService,UserServiceInterface $userService, CategoriesService $categoriesService )
    {
        $this->productService = $productService;
        $this->cartService = $cartService;
        $this->userService = $userService;
        $this->categoriesService = $categoriesService;
    }
    /**
     * @Route("/", name="shop_index")
     * @param Request $request
     * @return Response|null
     */
    public function indexAction(Request $request)
    {
        $currentUser = $this->userService->currentUser();
        $titlePage = "Home";
        $cartStatus = $this->cartService->findByCartStatus($currentUser->getId());
        $newProduct=$this->productService->newProducts();
        return $this->render('default/index.html.twig', [
            'newProduct' => $newProduct,
            'cartStatus' => $cartStatus,
            'titlePage' => $titlePage,
            'user' => $currentUser,
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
}
