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
        return $this->productRepository->create($product);
    }

    public function update(Product $product): bool
    {

        $this->checkPriceDiscount($product);
        return $this->productRepository->update($product);
    }

    public function delete(Product $product): bool
    {
        return $this->productRepository->remove($product);
    }

    public function allProducts()
    {
        return $this->productRepository->findBy([], ['id' => 'desc']);
    }

    public function searchForm($data)
    {
        return $this->productRepository->searchProcess($data);
    }

    public function getOneById(int $id)
    {
        return $this->productRepository->find($id);
    }

    public function newCollection($minP,$maxP,$minDiscount,$maxDiscount)
    {
        return $this->productRepository->collectionNew($minP,$maxP,$minDiscount,$maxDiscount);
    }

    public function newProducts()
    {
        return $this->productRepository->newProducts();
    }

    public function updateDiscountData(Product $product): bool
    {
        return $this->productRepository->update($product);
    }

    public function productsBy(int $id, $minP,$maxP,$minDiscount,$maxDiscount)
    {
        return $this->productRepository->productsByCategory($id, $minP,$maxP,$minDiscount,$maxDiscount);
    }

    /**
     * @param Product $product
     */
    private function checkPriceDiscount(Product $product): void
    {
        if ($product->getDiscount() === null) {
            $currentPrice = $product->getOldPrice();
            $product->setPrice($currentPrice);
            $product->setOldPrice(null);
            $product->setDiscount(0);
            $product->setStatus('0');
        } else {
            $product->setStatus('0');
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function minPrice($id)
    {
        $minMax[] = intval(min($this->productRepository->priceByCategory($id))['price']);
        $minMax[] = intval(max($this->productRepository->priceByCategory($id))['price']);
        return $minMax;
    }
    /**
     * @param $id
     * @return mixed
     */
    public function minDiscount($id)
    {
        $minMaxDiscount[] = intval(min($this->productRepository->discountByCategory($id))['discount']);
        $minMaxDiscount[] = intval(max($this->productRepository->discountByCategory($id))['discount']);
        return $minMaxDiscount;
    }

    /**
     * @return mixed
     */
    public function minPricePromotion()
    {
        $minMax[] = intval(min($this->productRepository->priceByPromotion())['price']);
        $minMax[] = intval(max($this->productRepository->priceByPromotion())['price']);
        return $minMax;
    }
    /**
     * @param $id
     * @return mixed
     */
    public function minMaxDiscount()
    {
        $minMaxDiscount[] = intval(min($this->productRepository->discountByPromotion())['discount']);
        $minMaxDiscount[] = intval(max($this->productRepository->discountByPromotion())['discount']);
        return $minMaxDiscount;
    }
    public function dateDiscount($products): void
    {
        foreach ($products as $product) {
            /**
             * @var Product $product
             */
            $today = date("d/m/Y");
            $startDate = date("d/m/Y", strtotime($product->getDiscountStart()));
            $dateEnd = date("d/m/Y", strtotime($product->getDiscountEnd()));
            if ($today < $startDate) {
                $product->setStatus('0');
                $this->updateDiscountData($product);
            }
            if (
                $product->getStatus() === '0'
                && $product->getDiscount() != 0
                && $today >= $startDate
                && $dateEnd > $today) {
                $currentPrice = $product->getPrice();
                $price = ceil($product->getPrice() - ($product->getPrice() * ($product->getDiscount() / 100)));
                $product->setPrice($price);
                $product->setOldPrice($currentPrice);
                $product->setStatus(1);
                $this->updateDiscountData($product);
            }
            if ($dateEnd <= $today && $product->getStatus() == 1 && $product->getDiscount() != null) {
                $currentPrice = $product->getOldPrice();
                $product->setPrice($currentPrice);
                $product->setOldPrice(null);
                $product->setDiscount('0');
                $product->setStatus('0');
                $this->updateDiscountData($product);
            }
        }
    }
}