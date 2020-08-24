<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
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
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(message = "This ( Title ) should not be blank.")
     * @Assert\Length(
     *      min = 4,
     *      minMessage = "Your ( Ttle ) must be at least {{ limit }} characters long."
     *  )
     */
    private $title;
    /**
     * @var Categories
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Categories",inversedBy="products")
     * @ORM\JoinColumn(name="category_id",referencedColumnName="id")
     ** @Assert\NotBlank(message = "This ( Categories ) should not be blank.")
     */
    private $category;
    /**
     * @var Metals
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Metals",inversedBy="products")
     * @ORM\JoinColumn(name="metal_id",referencedColumnName="id")
     * @Assert\NotBlank(message = "This ( Metal Type ) should not be blank.")
     */
    private $metalId;

    /**
     * @var string
     * @ORM\Column(name="size", type="string", nullable=true)
     */
    private $size;

    /**
     * @var float
     * @ORM\Column(name="price", type="decimal", scale=2)
     * @Assert\NotBlank(message = "This ( Metal Type ) should not be blank.")
     * @Assert\Regex(
     *     pattern="/\d{1,3}(?:[.,]\d{3})*(?:[.,]\d{2})?/",
     *     match=true,
     *     message="Your ( Price ) can contain only numbers."
     * )
     */
    private $price;
    /**
     * @var string
     * @ORM\Column(name="discount", type="string", nullable=true)
     */
    private $discount;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank(message = "This ( Description ) should not be blank.")
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "Your ( Description ) must be at least {{ limit }} characters long."
     *  )
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=255)
     * @Assert\NotBlank(message = "This ( Gender ) should not be blank.")
     *
     */
    private $gender;

    /**
     * @var float
     * @ORM\Column(name="scaleWeight", type="float")
     * @Assert\NotBlank(message = "This ( Scale Weight ) should not be blank.")
     * @Assert\Type(type="float", message="List scale weight must be a numeric value.")
     */
    private $scaleWeight;
    /**
     * @var string
     * @ORM\Column(name="image", type="text")
     */
    private $image;
    /**
     * @ORM\Column(name="dateAdded", type="datetime")
     * @var DateTime
     */
    private $dateAdded;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="products")
     * @ORM\JoinColumn(name="authorId",referencedColumnName="id")
     */
    private $author;

    public function __construct()
    {
        $this->dateAdded = new DateTime('now');
    }

    /**
     * @return DateTime
     */
    public function getDateAdded(): DateTime
    {
        return $this->dateAdded;
    }

    /**
     * @param DateTime $dateAdded
     * @return Product
     */
    public function setDateAdded(DateTime $dateAdded)
    {
        $this->dateAdded = $dateAdded;
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
     * Set title
     *
     * @param string $title
     *
     * @return Product
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $size
     * @return $this
     */
    public function setSize($size)
    {
            $this->size = $size;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param $discount
     * @return $this
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Product
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set scaleWeight
     *
     * @param float $scaleWeight
     *
     * @return Product
     */
    public function setScaleWeight($scaleWeight)
    {
        $this->scaleWeight = $scaleWeight;

        return $this;
    }

    /**
     * Get scaleWeight
     *
     * @return float
     */
    public function getScaleWeight()
    {
        return $this->scaleWeight;
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
     * @return Product
     */
    public function setImage(string $image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     * @return Product
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return Categories
     */
    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    /**
     * @param Categories $category
     * @return Product
     */
    public function setCategory(Categories $category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Metals
     */
    public function getMetalId(): ?Metals
    {
        return $this->metalId;
    }

    /**
     * @param Metals $metalId
     * @return Product
     */
    public function setMetalId(Metals $metalId)
    {
        $this->metalId = $metalId;
        return $this;
    }


}

