<?php

namespace AppBundle\Entity;

use AppBundle\Service\Product\ProductService;
use ClassesWithParents\D;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
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
     * @ORM\Column(name="oldPrice", type="decimal",options={"default" : 0}, scale=2)
     */
    private $oldPrice;
    /**
     * @var int
     * @ORM\Column(name="discount", type="integer",options={"default" : 0})
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

    /**
     * @var string|null
     * @ORM\Column(name="discount_start", type="string",options={"default" : 0})
     */
    private $discountStart;
    /**
     * @var string|null
     * @ORM\Column(name="discount_end", type="string",options={"default" : 0})
     */
    private $discountEnd;
    /**
     * @var string
     * @ORM\Column(name="status", type="string",options={"default" : 0})
     */
    private $status;
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->dateAdded = new DateTime('now');
        $this->productService=$productService;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Product
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getDiscountStart()
    {
        return $this->discountStart;
    }

    /**
     * @param string $discountStart
     * @return Product
     */
    public function setDiscountStart(string $discountStart)
    {
        $this->discountStart = $discountStart;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDiscountEnd()
    {
        return $this->discountEnd;
    }

    /**
     * @param string $discountEnd |null
     * @return $this
     */
    public function setDiscountEnd(string $discountEnd)
    {
        $this->discountEnd = $discountEnd;
        return $this;
    }


    /**
     * @return DateTime
     */
    public function getDateAdded()
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

    public function getOldPrice()
    {
        return $this->oldPrice;
    }

    /**
     * @param mixed $oldPrice
     * @return Product
     */
    public function setOldPrice($oldPrice)
    {
        $this->oldPrice = $oldPrice;
        return $this;
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

    /**
     * @param $string
     * @return false|string
     */
    public function formatDate($string)
    {
        $time = strtotime($string);
        return date('Y-m-d H:i:s', $time);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param $productDiscount
     * @return bool
     */
    function validateDate($startDate, $endDate, $productDiscount)
    {
        $dateToday = new DateTime(); // Today
        $todayDate = $dateToday->format('Y:m:d');
        $dateStart = date('Y:m:d', strtotime($startDate));
        $dateEnd = date('Y:m:d', strtotime($endDate));
        if ($dateStart <= $todayDate && $dateEnd >= $todayDate
            && $productDiscount > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $endDate
     * @param $discountCheck
     * @return bool
     */
    function stopDiscount($endDate, $discountCheck)
    {
        $dateToday = new DateTime(); // Today
        $todayDate = $dateToday->format('Y:m:d');
        $dateEnd = date('Y:m:d', strtotime($endDate));
        if ($dateEnd < $todayDate || $discountCheck === 0) {
            return true;
        }
        return false;
    }

    function checkStartDiscount($startDate, $discount)
    {
        $dateToday = new DateTime(); // Today
        $todayDate = $dateToday->format('Y:m:d');
        $dateStart = date('Y:m:d', strtotime($startDate));
        if ($dateStart === $todayDate && $discount > 0) {
            if ($this->getStatus()==='1'){
                return false;
            }
            return true;
        }
        return  false;
    }
}

