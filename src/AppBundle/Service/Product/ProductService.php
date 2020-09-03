<?php


namespace AppBundle\Service\Product;

use DateTime;
use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use AppBundle\Service\Users\UserServiceInterface;

class ProductService implements ProductServiceInterface
{
    private $productRepository;
    private $author;

    /**
     * @param $productRepository
     * @param $author
     */
    public function __construct(ProductRepository $productRepository, UserServiceInterface $author)
    {
        $this->productRepository = $productRepository;
        $this->author = $author;
    }

    public function insert(Product $product): bool
    {
        $product->setAuthor($this->author->currentUser());
        $product->setStatus(0);
        $this->checkDiscount($product);
        return $this->productRepository->create($product);
    }

    public function update(Product $product): bool
    {
        //  $product->setStatus('0');
        return $this->productRepository->update($product);
    }

    public function updateStartDiscount(Product $product): bool
    {
        // $this->checkDiscountUpdate($product);

        return $this->productRepository->update($product);
    }

    public function updateStopDiscount(Product $product): bool
    {
        return $this->productRepository->update($product);
    }

    public function delete(Product $product): bool
    {
        return $this->productRepository->remove($product);
    }

    public function allProducts()
    {
        return $this->productRepository->findAll();
    }

    public function searchForm( $data)
    {
        return $this->productRepository->searchProcess($data);
    }

    public function getOneById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }

    public function newCollection()
    {
        return $this->productRepository->collectionNew();
    }
    public function newProducts()
    {
        return $this->productRepository->newProducts();
    }
    public function updateDiscountData(Product $product): bool
    {
        return $this->productRepository->update($product);
    }

    public function productsBy(int $id)
    {
        return $this->productRepository->productsByCategory($id);
    }

    /**
     * @param Product $product
     */
    private function checkDiscount(Product $product): void
    {
        $dateToday = new DateTime(); // Today
        $todayDate = $dateToday->format('Y:m:d');
        $dateStart = date('Y:m:d', strtotime($product->getDiscountStart()));
        $discount = intval($product->getDiscount());
        if ($product->getDiscount() !== null || $todayDate === $dateStart) {
            $price = floatval($product->getPrice());
            if ($todayDate === $dateStart) {
                if ($product->getDiscount() !== null) {
                    $product->setPrice($price - ($price * ($discount / 100)));
                    $product->setOldPrice($price);
                    $product->setStatus('1');
                } else {
                    $product->setOldPrice(0);
                }
            } else {
                $product->setOldPrice(0);
            }
        }
    }


}