<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table(name="carts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartRepository")
 */
class Cart
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
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User",inversedBy="carts")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     */
    private $userId;
    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product",inversedBy="carts")
     * @ORM\JoinColumn(name="product_id",referencedColumnName="id")
     */
    private $productId;
    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="totalSum", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalSum;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="integer",options={"default" : 0})
     */
    private $status;
    /**
     * @ORM\Column(name="dateAdded", type="datetime")
     * @var DateTime
     */
    private $dateAdded;

    public function __construct()
    {
        $this->dateAdded = new DateTime('now');
    }
    public function __toString()
    {
        return "";
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
     * @return DateTime
     */
    public function getDateAdded(): DateTime
    {
        return $this->dateAdded;
    }

    /**
     * @param DateTime $dateAdded
     */
    public function setDateAdded(DateTime $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return User
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param User $userId
     * @return Cart
     */
    public function setUserId(User $userId)
    {
        $this->userId = $userId;
        return $this;
    }


    /**
     * @return Product
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param Product $productId
     * @return Cart
     */
    public function setProductId(Product $productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return Cart
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set totalSum
     *
     * @param string $totalSum
     *
     * @return Cart
     */
    public function setTotalSum($totalSum)
    {
        $this->totalSum = $totalSum;

        return $this;
    }

    /**
     * Get totalSum
     *
     * @return string
     */
    public function getTotalSum()
    {
        return $this->totalSum;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Cart
     */
    public function setStatus($status=0)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}

