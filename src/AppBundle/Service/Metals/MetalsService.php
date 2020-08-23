<?php


namespace AppBundle\Service\Metals;


use AppBundle\Entity\Metals;
use AppBundle\Repository\MetalsRepository;
use AppBundle\Service\Users\UserServiceInterface;

class MetalsService implements MetalsServiceInterface
{
    private $metalsRepository;
    private $author;

    /**
     * MetalsService constructor.
     * @param $metalsRepository
     * @param $author
     */
    public function __construct(MetalsRepository $metalsRepository, UserServiceInterface $author)
    {
        $this->metalsRepository = $metalsRepository;
        $this->author = $author;
    }

    public function insert(Metals $metals): bool
    {
        $metals->setAuthor($this->author->currentUser());
        return $this->metalsRepository->create($metals);
    }

    public function update(Metals $metals): bool
    {
        return $this->metalsRepository->update($metals);
    }

    public function delete(Metals $metals): bool
    {
        return $this->metalsRepository->remove($metals);
    }

    public function getTitle(string $metals): ?Metals
    {
        return $this->metalsRepository->findOneBy(['title' => $metals]);
    }

    public function getOneMetal(int $id): ?Metals
    {
        return $this->metalsRepository->find($id);
    }

    public function getAllMetals()
    {
        return $this->metalsRepository->findAll();
    }
}