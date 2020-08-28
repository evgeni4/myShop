<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Categories;
use AppBundle\Entity\Product;
use AppBundle\Form\CartType;
use AppBundle\Form\ProductType;
use AppBundle\Service\Cart\CartService;
use AppBundle\Service\Categories\CategoriesService;
use AppBundle\Service\Product\ProductService;
use AppBundle\Service\Users\UserService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
class PagesController extends Controller
{
    private $productService;
    private $userService;
    private $categoriesService;
    private $cartService;

    /**
     * ProductController constructor.
     * @param CartService $cartService
     * @param ProductService $productService
     * @param UserService $userService
     * @param CategoriesService $categoriesService
     */
    public function __construct(CartService $cartService,ProductService $productService, UserService $userService,CategoriesService $categoriesService
    )
    {
        $this->cartService = $cartService;
        $this->productService = $productService;
        $this->userService = $userService;
        $this->categoriesService = $categoriesService;

    }

    /**
     * @Route("/views/result", name="search_product", methods={"POST"})
     * @param Request $request
     * @return Response|null
     */
    public function search(Request $request)
    {
        $currentUser = $this->userService->currentUser();
        $data=$request->request->get("product");
        $collections = $this->productService->searchForm($data['title']);
        $catId=$this->categoriesService->getAllCategory();
       $titlePage = "Search Result";
//        $this->validStartDiscount($collections);
//        $this->productValidDiscount($collections);
        return $this->render('pages/search_views.html.twig',
            [
                'collections' => $collections,
                'user'=>$currentUser,
                'titlePage'=>$titlePage,
            ]);
    }



    /**
     * @Route("/promotion", name="new_promotion", methods={"GET"})
     * @return Response|null
     */
    public function promotion()
    {
        $currentUser = $this->userService->currentUser();
        $collections = $this->productService->newCollection();
        $this->validStartDiscount($collections);
        $this->productValidDiscount($collections);
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
        $this->validStartDiscount($collections);
        $this->productValidDiscount($collections);
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
//        $this->productValidDiscountProcess((array)$collections);
        $cartStatus = $this->cartService->findByCartStatus($currentUser->getId());
        return $this->render('pages/page_view.html.twig',
            [
                'cartStatus' => $cartStatus,
                'product' => $product,
                'user'=>$currentUser,
                'titlePage'=>$titlePage,
                'form'=>$this->createForm(CartType::class)->createView()
            ]);
    }
    /**
     * @param array $products
     */
    private function productValidDiscount(array $products): void
    {
        foreach ($products as $product) {
            $dateToday = new DateTime(); // Today
            $todayDate = $dateToday->format('Y:m:d');
            $dateEnd = date('Y:m:d', strtotime($product->getDiscountEnd()));
            if ($todayDate === $dateEnd || $todayDate > $dateEnd) {
                $product->setPrice($product->getOldPrice());
                $product->setOldPrice(0);
                $product->setDiscountStart('0');
                $product->setDiscountEnd('0');
                $product->setStatus('0');
                $this->productService->updateStopDiscount($product);
            }
        }
    }
    /**
     * @param array $products
     */
    private function validStartDiscount(array $products): void
    {
        foreach ($products as $product) {
            $dateToday = new DateTime(); // Today
            $todayDate = $dateToday->format('Y:m:d');
            $dateStart = date('Y:m:d', strtotime($product->getDiscountStart()));
            if ($todayDate == $dateStart || $todayDate > $dateStart && $product->getStatus() == '0') {
                $price = floatval($product->getPrice());
                $discount = intval($product->getDiscount());
                $product->setPrice($price - ($price * ($discount / 100)));
                $product->setOldPrice($price);
                $product->setStatus('1');
                $this->productService->updateStartDiscount($product);
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
