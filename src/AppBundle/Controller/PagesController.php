<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Categories;
use AppBundle\Service\Categories\CategoriesService;
use AppBundle\Service\Product\ProductService;
use AppBundle\Service\Users\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends Controller
{
    private $productService;
    private $userService;
    private $categoriesService;

    /**
     * ProductController constructor.
     * @param ProductService $productService
     * @param UserService $userService
     * @param CategoriesService $categoriesService
     */
    public function __construct(ProductService $productService, UserService $userService,CategoriesService $categoriesService
    )
    {
        $this->productService = $productService;
        $this->userService = $userService;
        $this->categoriesService = $categoriesService;
    }
    /**
     * @Route("/promotion", name="new_promotion", methods={"GET"})
     * @return Response|null
     */
    public function promotion()
    {
        $currentUser = $this->userService->currentUser();
        $collections = $this->productService->newCollection();
        $this->productValidDiscountProcess((array)$collections);
        $titlePage="Promotion";
        return $this->render('pages/page_collection.html.twig',
            [
                'collections' => $collections,
                'user'=>$currentUser,
                'titlePage'=>$titlePage
            ]);
    }

    /**
     * @Route("/views/{id}", name="views_product_category", methods={"GET"})
     * @param int $id
     * @return Response|null
     */
    public function views(int $id)
    {
        $currentUser = $this->userService->currentUser();
        $collections = $this->productService->productsBy($id);
        $catId=$this->categoriesService->getAllCategory();
        $titlePage = $this->titlePage($catId, $id);
        $this->productValidDiscountProcess((array)$collections);
        return $this->render('pages/page_views.html.twig',
            [
                'collections' => $collections,
                'user'=>$currentUser,
                'titlePage'=>$titlePage,
            ]);
    }

    /**
     * @Route("/views/product/{id}", name="view_product", methods={"GET"})
     * @param int $id
     * @return Response|null
     */
    public function viewsProduct(int $id)
    {
        $currentUser = $this->userService->currentUser();
        $collections = $this->productService->productsBy($id);
        $product = $this->productService->getOneById($id);
        $catId=$this->categoriesService->getAllCategory();
        $titlePage = $this->titlePage($catId, $id);
        $this->productValidDiscountProcess((array)$collections);
        return $this->render('pages/page_view.html.twig',
            [
                'product' => $product,
                'user'=>$currentUser,
                'titlePage'=>$titlePage,
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
                $product->setPrice($product->getOldPrice());
                $product->setOldPrice(intval('0'));
                $product->setDiscountStart('0');
                $product->setDiscountEnd('0');
                $product->setStatus('0');

                $this->productService->update($product);
            }
            if ($product->getStatus()==='0'){
                if ($product->checkStartDiscount($product->getDiscountStart(), $product->getDiscount())) {

                    $currentPrice = $product->getPrice();
                    $discount = intval($product->getDiscount());
                    $price = $currentPrice - ($currentPrice * ($discount / 100));

                    $product->setPrice($price);
                    $product->setOldPrice($currentPrice);
                    $this->productService->updateStartDiscount($product);
                }
            }
        }
    }

    /**
     * @param array $catId
     * @param int $id
     * @return string
     */
    private function titlePage(array $catId, int $id): string
    {
        $title = "";
        foreach ($catId as $cat) {
            if ($cat->getId() === $id) {
                $title = $cat->getTitle();
            }
        }
        return $title;
    }
}
