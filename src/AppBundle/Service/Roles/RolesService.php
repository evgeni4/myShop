<?php
namespace AppBundle\Service\Roles;



use AppBundle\Repository\RoleRepository;

class RolesService implements RolesServiceInterface
{
    /**
     * @var
     */
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function findOneBy(string $criteria)
    {
        return $this->roleRepository->findOneBy(['name'=>$criteria]);
    }
}