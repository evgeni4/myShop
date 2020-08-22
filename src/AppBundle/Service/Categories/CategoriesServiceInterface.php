<?php


namespace AppBundle\Service\Categories;


use AppBundle\Entity\Categories;

interface CategoriesServiceInterface
{
    public function insert(Categories $categories): bool;

    public function update(Categories $categories): bool;

    public function delete(Categories $categories): bool;

    public function getTitle(string $title): ?Categories;

    public function getUrl(string $url): ?Categories;

    public function getOneCategory(int $id): ?Categories;

    public function getAllCategory();
}