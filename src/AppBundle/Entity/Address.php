<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Address
 *
 * @ORM\Table(name="address_users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddressRepository")
 */
class Address
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="fullName", type="string", length=255)
     *
     * @Assert\NotBlank(message = "This ( Full Name ) should not be blank.")
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "Your Full name must be at least {{ limit }} characters long"
     *  )
     */
    private $fullName;

    /**
     * @var string
     * @Assert\NotBlank(message = "This ( City / town ) should not be blank.")
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Your City / town must be at least {{ limit }} characters long"
     *  )
     * @ORM\Column(name="populated", type="string", length=255)
     */
    private $populated;

    /**
     * @var string
     * @Assert\NotBlank(message = "This ( Address ) should not be blank.")
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "Your Address must be at least {{ limit }} characters long"
     *  )
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string
     * @Assert\NotBlank(message = "This ( Post code ) should not be blank.")
     * @Assert\Regex(
     *     pattern="/^[0-9]{3,6}$/",
     *     match=true,
     *     message="Your ( Post code ) cannot contain a number ( limit 3-6 )."
     * )
     * @ORM\Column(name="postCode", type="string", length=255)
     */
    private $postCode;

    /**
     * @var int
     * @Assert\NotBlank(message = "This ( Phone ) should not be blank.")
     * @Assert\Regex(
     *     pattern="/^[0-9]{10}$/",
     *     match=true,
     *     message="Your ( Phone ) cannot contain a number ( limit 10 )."
     * )
     * @ORM\Column(name="phone", type="integer", unique=true)
     */
    private $phone;
    /**
     * @var User
     * @ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="address")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     * @return Address
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;
        return $this;
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
     * Set fullName
     *
     * @param string $fullName
     *
     * @return Address
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set populated
     *
     * @param string $populated
     *
     * @return Address
     */
    public function setPopulated($populated)
    {
        $this->populated = $populated;

        return $this;
    }

    /**
     * Get populated
     *
     * @return string
     */
    public function getPopulated()
    {
        return $this->populated;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Address
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set postCode
     *
     * @param string $postCode
     *
     * @return Address
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * Get postCode
     *
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * Set phone
     *
     * @param integer $phone
     *
     * @return Address
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return int
     */
    public function getPhone()
    {
        return $this->phone;
    }

}

