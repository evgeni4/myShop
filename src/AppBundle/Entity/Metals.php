<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Metals
 *
 * @ORM\Table(name="metals")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MetalsRepository")
 */
class Metals
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
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     * @Assert\NotBlank(message = "This (Title ) should not be blank.")
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Your ( Title ) must be at least {{ limit }} characters long"
     *  )
     * @Assert\Regex(
     *     pattern="/^[A-Za-z\s]{3,}$/",
     *     match=true,
     *     message="Your ( Title ) can contain a text ( min 3 ) without spaces and numbers."
     * )
     */
    private $title;
    /**
     * @var DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

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
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User",inversedBy="metals")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    /**
     * @param DateTime $dateAdded
     */
    public function setDateAdded(DateTime $dateAdded): void
    {
        $this->dateAdded = $dateAdded;
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
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     * @return Metals
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Metals
     */
    public function setTitle($title)
    {
        $this->title = strtolower($title);

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
}

