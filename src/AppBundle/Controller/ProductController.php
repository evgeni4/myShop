<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use AppBundle\Service\Metals\MetalsService;
use AppBundle\Service\Product\ProductService;
use AppBundle\Service\Users\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends Controller
{
    private $productService;
    private $userService;
    private $metalService;

    /**
     * ProductController constructor.
     * @param ProductService $productService
     * @param UserService $userService
     * @param MetalsService $metalService
     */
    public function __construct(ProductService $productService, UserService $userService, MetalsService $metalService
    )
    {
        $this->productService = $productService;
        $this->userService = $userService;
        $this->metalService = $metalService;
    }

    /**
     * @Route("/dashboard/products", name="all_products", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response|null
     */
    public function products()
    {
        $currentUser = $this->userService->currentUser();
        $products = $this->productService->allProducts();
        $this->productValidDiscountProcess($products);
        if ($currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        return $this->render('products/all_product.html.twig',
            [
                'user' => $currentUser
            ]
        );
    }

    /**
     * @Route("/dashboard/products/add", name="add_product", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function addProduct()
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();
        $metals = $this->metalService->getAllMetals();
        if ($currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        return $this->render('products/add_product.html.twig',
            [
                'metals' => $metals,
                'user' => $currentUser,
                'errors' => $messages,
                'form' => $this->createForm(ProductType::class)->createView()
            ]
        );
    }

    /**
     * @Route("/dashboard/products/add", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return RedirectResponse|Response|null
     */
    public function addProductProcess(Request $request)
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();
        if ($currentUser->isUser()) {
            return $this->redirectToRoute('shop_index');
        }
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $messages = $this->errorCollection($form, $messages);
        $files = $request->files->get('product')['image'];

        if ($form->isValid() && $form->isSubmitted()) {
            $this->uploadImages($files, $product);
            $this->productService->insert($product);
            $this->addFlash('info', "Added product successfully!");
            return $this->redirectToRoute('all_products');
        }
        return $this->render('products/add_product.html.twig',
            [
                'user' => $currentUser,
                'errors' => $messages,
                'form' => $this->createForm(ProductType::class)->createView()
            ]
        );
    }


    /**
     * @Route("/dashboard/products/edit/{id}", name="edit_product", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param int $id
     * @return RedirectResponse|Response|null
     */
    public function editProduct(int $id)
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();
        $metals = $this->metalService->getAllMetals();
        $product = $this->productService->getOneById($id);
        if (!$currentUser->isAdmin() && !$currentUser->isAuthorProduct($product)) {
            return $this->redirectToRoute('all_products');
        }
        if (null === $product) {
            return $this->redirectToRoute('all_products');
        }
        return $this->render('products/edit_product.html.twig',
            [
                'product' => $product,
                'metals' => $metals,
                'user' => $currentUser,
                'errors' => $messages,
                'form' => $this->createForm(ProductType::class)->createView()
            ]
        );
    }

    /**
     * @Route("/dashboard/products/edit/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response|null
     */
    public function editProductProcess(Request $request, int $id)
    {
        $messages = [];
        $currentUser = $this->userService->currentUser();
        $product = $this->productService->getOneById($id);
        $form = $this->createForm(ProductType::class, $product);
        $data = $request->request->get('product');

        $this->checkDiscountEmpty($data, $product);
        $form->handleRequest($request);
        if (!$currentUser->isAdmin() && !$currentUser->isAuthorProduct($product)) {
            return $this->redirectToRoute('all_products');
        }
        if (null === $product) {
            return $this->redirectToRoute('all_products');
        }
        $messages = $this->errorCollection($form, $messages);
        $files = $request->files->get('product')['image'];
        if ($form->isValid() && $form->isSubmitted()) {
            $this->uploadImages($files, $product);
            $this->productService->update($product);
            $this->addFlash('info', "Edit product successfully!");
            return $this->redirectToRoute('all_products');
        }
        return $this->render('products/edit_product.html.twig',
            [
                'user' => $currentUser,
                'errors' => $messages,
                'form' => $this->createForm(ProductType::class)->createView()
            ]
        );
    }


    /**
     * @Route("/dashboard/products/delete/{id}", name="delete_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response|null
     */
    public function deleteProduct(Request $request, int $id)
    {
        $currentUser = $this->userService->currentUser();
        $product = $this->productService->getOneById($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if (!$currentUser->isAdmin() && !$currentUser->isAuthorProduct($product)) {
            return $this->redirectToRoute('all_products');
        }
        if (null === $product) {
            return $this->redirectToRoute('all_categories');
        }
        $this->productService->delete($product);
        $this->addFlash('info', "Edit product successfully!");
        return $this->redirectToRoute('all_products');

    }

    /**
     * @param FormInterface $form
     * @param array $messages
     * @return array
     */
    private function errorCollection(FormInterface $form, array $messages): array
    {
        foreach ($form->getErrors(true) as $err) {
            $messages[] = $err->getMessage();
        }
        return $messages;
    }

    /**
     * @param $files
     * @param Product $product
     */
    private function uploadImages($files, Product $product): void
    {
        if (!empty($files)) {
            $fileUp = [];
            foreach ($files as $file) {
                $fileName = md5(uniqid()) . "." . $file->guessExtension();
                $file->move(
                    $this->getParameter('products_image'),
                    $fileName);
                $fileUp[] = $fileName;
            }

            $product->setImage(implode(',', $fileUp));
        }
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
     * @param $data
     * @param Product|null $product
     */
    private function checkDiscountEmpty($data, ?Product $product): void
    {

        if ($data['discountStart'] === '') {
            $data['discountStart'] = $product->getDiscountStart();
        }
        if ($data['discountEnd'] === '') {
            $data['discountStart'] = $product->getDiscountEnd();
        }
    }
}
