<?php

namespace AppBundle\Service\Users;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\Encryption\ArgonEncryption;
use AppBundle\Service\Roles\RolesServiceInterface;
use Symfony\Component\Security\Core\Security;

class UserService implements UserServiceInterface
{
    private $security;
    private $userRepository;
    private $encryptionService;
    private $roleService;

    public function __construct(Security $security,
                                UserRepository $userRepository,
                                ArgonEncryption $encryptionService,
                                RolesServiceInterface $roleService
    )
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->encryptionService = $encryptionService;
        $this->roleService = $roleService;
    }

    /**
     * @param string $email
     * @return User|null |object
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    /**
     * @param User $user
     * @param $count
     * @return bool
     */
    public function save(User $user, $count): bool
    {

        $passwordHash = $this->encryptionService->hash($user->getPassword());
        $user->setPassword($passwordHash);
        $role = $this->checkNewUserRoles();
        $roleUser = $this
            ->roleService
            ->findOneBy($role);
        $user->addRoles($roleUser);
        return $this->userRepository->insert($user);
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function findOneById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    /**
     * @param User $user
     * @return User|null
     */
    public function findOne(User $user): ?User
    {
        return $this->userRepository->find($user);
    }

    /**
     * @return User|null |object
     */
    public function currentUser(): ?User
    {
        return $this->security->getUser();
    }

    public function update(User $user): bool
    {
        return $this->userRepository->update($user);
    }

    public function allUser()
    {
        return $this->userRepository->findAll();
    }

    /**
     * @return string
     */
    private function checkNewUserRoles(): string
    {
        if (count($this->allUser()) == 0 || count($this->allUser()) == null) {
            $role = "ROLE_ADMIN";
        } else if (count($this->allUser()) == 1) {
            $role = "ROLE_SALES";
        } else {
            $role = "ROLE_USER";
        }
        return $role;
    }

    public function getCustomer()
    {
        return $this->userRepository->allCustomers();
    }
}