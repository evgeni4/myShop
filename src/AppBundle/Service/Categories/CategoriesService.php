<?php


namespace AppBundle\Service\Categories;


use AppBundle\Entity\Categories;
use AppBundle\Repository\CategoriesRepository;
use AppBundle\Service\Users\UserServiceInterface;

class CategoriesService implements CategoriesServiceInterface
{
    private $categoriesRepository;
    private $author;

    /**
     * CategoriesService constructor.
     * @param CategoriesRepository $categoriesRepository
     * @param UserServiceInterface $author
     */
    public function __construct(CategoriesRepository $categoriesRepository, UserServiceInterface $author)
    {
        $this->categoriesRepository = $categoriesRepository;
        $this->author = $author;
    }

    public function insert(Categories $categories): bool
    {
        $categories->setAuthor($this->author->currentUser());
        return $this->categoriesRepository->create($categories);
    }

    public function update(Categories $categories): bool
    {
        return $this->categoriesRepository->update($categories);
    }

    public function delete(Categories $categories): bool
    {
       return $this->categoriesRepository->remove($categories);
    }

    /**
     * @param string $title
     * @return Categories|null
     */
    public function getTitle(string $title): ?Categories
    {
        return $this->categoriesRepository->findOneBy(['title' => $title]);
    }

    /**
     * @param string $url
     * @return Categories|null
     */
    public function getUrl(string $url): ?Categories
    {
        return $this->categoriesRepository->findOneBy(['url' => $url]);
    }

    public function getAllCategory()
    {
        return $this->categoriesRepository->findAll();
    }

    public function getOneCategory(int $id): ?Categories
    {
        return $this->categoriesRepository->find($id);
    }
}