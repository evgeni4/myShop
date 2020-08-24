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
     * @Assert\NotBlank(message = "This ( Email ) should not be blank.")
     * @Assert\Email(message = "This ( Email ) is not a valid.")
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank(message = "This ( Password ) should not be blank.")
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "Your password must be at least {{ limit }} characters long"
     *  )
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;
    /**
     * @var string
     * @Assert\NotBlank(message = "This ( Full Name ) should not be blank.")
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "Your Full name must be at least {{ limit }} characters long"
     *  )
     * @ORM\Column(name="fullName", type="string", length=255)
     * @var string
     */
    private $fullName;
    /**
     * @ORM\Column(name="image", type="string", length=255)
     * @var string
     */
    private $image;
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
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Address", mappedBy="author")
     */
    private $address;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Categories", mappedBy="author")
     */
    private $categories;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Metals", mappedBy="author")
     */
    private $metals;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="author")
     */
    private $product;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->address = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->metals = new ArrayCollection();
        $this->product = new ArrayCollection();
    }
    public function __toString()
    {
        return "";
    }
    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image)
    {
        $this->image = $image;
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
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
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

    /**
     * @return ArrayCollection
     */
    public function getAddress(): ArrayCollection
    {
        return $this->address;
    }

    /**
     * @param Address $address
     * @return User
     */
    public function addAddress(Address $address)
    {
        $this->address[] = $address;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCategories(): ArrayCollection
    {
        return $this->categories;
    }

    /**
     * @param ArrayCollection $categories
     * @return User
     */
    public function setCategories(ArrayCollection $categories)
    {
        $this->categories[] = $categories;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getProduct(): ArrayCollection
    {
        return $this->product;
    }

    /**
     * @param ArrayCollection $product
     * @return User
     */
    public function setProduct(ArrayCollection $product)
    {
        $this->product[] = $product;
        return $this;
    }

    /**
     * @param Categories $categories
     * @return bool
     */
    public function isAuthorCategory(Categories $categories)
    {
        return $categories->getAuthor()->getId() == $this->getId();
    }

    /**
     * @param Metals $metal
     * @return bool
     */
    public function isAuthorMetal(Metals $metal)
    {
        return $metal->getAuthor()->getId() == $this->getId();
    }

    /**
     * @return ArrayCollection
     */
    public function getMetals(): ArrayCollection
    {
        return $this->metals;
    }

    /**
     * @param ArrayCollection $metals
     * @return User
     */
    public function setMetals(ArrayCollection $metals)
    {
        $this->metals[] = $metals;
        return $this;
    }

    /**
     * @param Address $address
     * @return bool
     */
    public function isAuthor(Address $address)
    {
        return $address->getAuthor()->getId() == $this->getId();
    }
    /**
     * @param Product $product
     * @return bool
     */
    public function isAuthorProduct(Product $product)
    {
        return $product->getAuthor()->getId() == $this->getId();
    }


    /**
     * @param Address $address
     * @param User $currentUser
     * @return bool
     */
    public function isAuthorOrAdmin(Address $address, User $currentUser)
    {
        if (!$currentUser->isAuthor($address) && !$currentUser->isAdmin()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array("ROLE_ADMIN", $this->getRoles());
    }

    /**
     * @return bool
     */
    public function isSales()
    {
        return in_array("ROLE_SALES", $this->getRoles());
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

