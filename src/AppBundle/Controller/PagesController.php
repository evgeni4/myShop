<?php

namespace AppBundle\Controller;

use AppBundle\Service\Product\ProductService;
use AppBundle\Service\Users\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends Controller
{
    private $productService;
    private $userService;

    /**
     * ProductController constructor.
     * @param ProductService $productService
     * @param UserService $userService
     */
    public function __construct(ProductService $productService, UserService $userService
    )
    {
        $this->productService = $productService;
        $this->userService = $userService;
    }
    /**
     * @Route("/collection", name="new_collection", methods={"GET"})
     * @return Response|null
     */
    public function newCollection()
    {
        $currentUser = $this->userService->currentUser();
        $collections = $this->productService->newCollection();
        $this->productValidDiscountProcess((array)$collections);
        return $this->render('pages/page_collection.html.twig',
            [
                'collections' => $collections,
                'user'=>$currentUser
            ]);
    }

    /**
     * @param array $products
     */
    private function productValidDiscountProcess(array $products): void
    {
        foreach ($products as $product) {
            if ($product->stopDiscount($product->getDiscountEnd(), $product->getDiscount())) {
                $product = $this->productService->getOneById($product->getId());
                $product->setDiscount('0');
                $product->setNewPrice(intval('0'));
                $product->setDiscountStart('0');
                $product->setDiscountEnd('0');
                $this->productService->update($product);
            }
            if ($product->checkStartDiscount($product->getDiscountStart(), $product->getDiscount())) {
                $price = $product->getPrice();
                $discount = intval($product->getDiscount());
                $priceNew = $price - ($price * ($discount / 100));
                $product->setNewPrice($priceNew);
                $this->productService->update($product);
            }
        }
    }
}
