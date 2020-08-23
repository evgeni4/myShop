<?php


namespace AppBundle\Service\Metals;


use AppBundle\Entity\Metals;

interface MetalsServiceInterface
{
    public function insert(Metals $metals): bool;

    public function update(Metals $metals): bool;

    public function delete(Metals $metals): bool;

    public function getTitle(string $metals): ?Metals;

    public function getOneMetal(int $id): ?Metals;

    public function getAllMetals();
}