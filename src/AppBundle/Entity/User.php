<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements \Symfony\Component\Security\Core\User\UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "Your password must be at least {{ limit }} characters long"
     *  )
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;
    /**
     * @Assert\NotBlank()
     *
     * @var ArrayCollection;
     * @ManyToMany(targetEntity="AppBundle\Entity\Role")
     * @ORM\JoinTable(name="users_roles",
     *  joinColumns={@ORM\JoinColumn(name="user_id",referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="role_id",referencedColumnName="id")}
     * )
     */
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * @return array(Role|string)[]
     */
    public function getRoles()
    {
        $strRoles = [];
        /**
         * @var Role $role
         */
        foreach ($this->roles as $role) {
            $strRoles[] = $role->getRole();
        }
        return $strRoles;
    }

    /**
     * @param Role $role
     * @return User
     */
    public function addRoles($role)
    {
        $this->roles[] = $role;
        return $this;
    }
    public function isUser()
    {
        return in_array("ROLE_USER", $this->getRoles());
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}

