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
    public function __construct(CartService $cartService, ProductService $productService, UserService $userService, CategoriesService $categoriesService
    )
    {
        $this->cartService = $cartService;
        $this->productService = $productService;
        $this->userService = $userService;
        $this->categoriesService = $categoriesService;

    }

    /**
     * @Route("/search/result", name="search_product")
     * @param Request $request
     * @return Response|null
     */
    public function search(Request $request)
    {
        $currentUser = $this->userService->currentUser();
        $data = $request->request->get("product");
        $collections = $this->productService->searchForm($data['title']);
        $this->productService->dateDiscount($collections);
        $paginator = $this->get('knp_paginator');
        $titlePage = "Search Result";
        $pagination = $paginator->paginate(
            $collections,
            $request->query->getInt('page', 1),
        );
        return $this->render('pages/search_views.html.twig',
            [
                'collections' => $pagination,
                'user' => $currentUser,
                'titlePage' => $titlePage,
            ]);
    }


    /**
     * @Route("/promotion", name="new_promotion")
     * @param Request $request
     * @return Response|null
     */
    public function promotion(Request $request)
    {
        $currentUser = $this->userService->currentUser();
        $minPrice = $this->productService->minPricePromotion();
        $minMaxDiscount = $this->productService->minMaxDiscount();

       list($minP, $maxP, $minDiscount, $maxDiscount) = $this->filterProduct($minPrice, $minMaxDiscount, $request);
        $collections = $this->productService->newCollection($minP,$maxP,$minDiscount,$maxDiscount);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $collections,
            $request->query->getInt('page', 1),
            3
        );
        $this->productService->dateDiscount($collections);
        $titlePage = "Promotion";
        return $this->render('pages/page_collection.html.twig',
            [
                'minDiscount' => $minMaxDiscount,
                'minPrice' => $minPrice,
                'collections' => $pagination,
                'user' => $currentUser,
                'titlePage' => $titlePage
            ]);
    }

    /**
     * @Route("/views/{id}", name="views_product_category")
     * @param Request $request
     * @param int $id
     * @return Response|null
     */
    public function views(Request $request, int $id)
    {
        $currentUser = $this->userService->currentUser();

        $minPrice = $this->productService->minPrice(intval($id));
        $minMaxDiscount = $this->productService->minDiscount(intval($id));

        list($minP, $maxP, $minDiscount, $maxDiscount) = $this->filterProduct($minPrice, $minMaxDiscount, $request);
        $collections = $this->productService->productsBy(intval($id),$minP,$maxP,$minDiscount,$maxDiscount);


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $collections,
            $request->query->getInt('page', 1), 3);
        $catId = $this->categoriesService->getAllCategory();
        $titlePage = $this->titlePage($catId, $id);
        $this->productService->dateDiscount($collections);

        return $this->render('pages/page_views.html.twig',
            [
                'minDiscount' => $minMaxDiscount,
                'minPrice' => $minPrice,
                'collections' => $pagination,
                'user' => $currentUser,
                'titlePage' => $titlePage,
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
        $product = $this->productService->getOneById($id);
        $catId = $this->categoriesService->getAllCategory();
        $titlePage = $this->titlePage($catId, $id);
        $this->productService->dateDiscount($this->productService->allProducts());

        return $this->render('pages/page_view.html.twig',
            [
                'product' => $product,
                'user' => $currentUser,
                'titlePage' => $titlePage,
                'form' => $this->createForm(CartType::class)->createView()
            ]);
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

    /**
     * @param $minPrice
     * @param $minMaxDiscount
     * @param Request $request
     * @return array
     */
    public function filterProduct($minPrice, $minMaxDiscount, Request $request): array
    {
        $minP = intval(min($minPrice));
        $maxP = intval(max($minPrice));
        $minDiscount = intval(min($minMaxDiscount));
        $maxDiscount = intval(max($minMaxDiscount));
        if ($request->isMethod('POST')) {
            $data = $request->request->get('sort');
            $dataPrice = explode("-", (str_replace(['$', ' '], '', $data['price'])));
            $dataDiscount = explode("-", (str_replace(['%', ' '], '', $data['discount'])));

            $minP = intval($dataPrice[0]);
            $maxP = intval($dataPrice[1]);
            $minDiscount = intval($dataDiscount[0]);
            $maxDiscount = intval($dataDiscount[1]);
        }
        return array($minP, $maxP, $minDiscount, $maxDiscount);
    }
}
