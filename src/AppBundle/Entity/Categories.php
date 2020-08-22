<?php

namespace AppBundle\Entity;


use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Categories
 *
 * @ORM\Table(name="catrgories")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoriesRepository")
 */
class Categories
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
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message = "This (Title ) should not be blank.")
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Your ( Title ) must be at least {{ limit }} characters long"
     *  )
     * @Assert\Regex(
     *     pattern="/^[A-Za-z]{3,}$/",
     *     match=true,
     *     message="Your ( Title ) can contain a text ( limit 3 ) without spaces and numbers."
     * )
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message = "This ( Slug/Url ) should not be blank.")
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Your ( Slug/Url ) must be at least {{ limit }} characters long"
     *  )
     * @Assert\Regex(
     *     pattern="/^[a-z]{3,}$/",
     *     match=true,
     *     message="Your ( Slug/Url ) can contain a text ( limit 3 ) without spaces and numbers."
     * )
     */
    private $url;
    /**
     * @var DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="categories")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
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
     */
    public function setDateAdded(DateTime $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
    }


    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     * @return Categories
     */
    public function setAuthor(User $author =null)
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
     * Set title
     *
     * @param string $title
     *
     * @return Categories
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
     * Set url
     *
     * @param string $url
     *
     * @return Categories
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}

