<?php


namespace AppBundle\Service\Product;


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
    public function __construct(ProductRepository $productRepository,UserServiceInterface $author)
    {
        $this->productRepository = $productRepository;
        $this->author = $author;
    }

    public function insert(Product $product): bool
    {
        $product->setAuthor($this->author->currentUser());
        return $this->productRepository->create($product);
    }

    public function update(Product $product): bool
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

    public function getOneById(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }
}