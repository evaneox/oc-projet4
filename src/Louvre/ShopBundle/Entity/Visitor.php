<?php

namespace Louvre\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Louvre\ShopBundle\Validator\BirthdayCheck;

/**
 * Visitor
 *
 * @ORM\Table(name="visitor")
 * @ORM\Entity(repositoryClass="Louvre\ShopBundle\Repository\VisitorRepository")
 */
class Visitor
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
     * @var
     * @ORM\ManyToOne(targetEntity="Louvre\ShopBundle\Entity\TicketOrder", inversedBy="visitors")
     * @ORM\JoinColumn(name="ticketOrder", referencedColumnName="id", nullable=false)
     */
    private $ticketOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="name.blank")
     * @Assert\Regex( pattern="#^(?!-)[\p{L}- ]{2,20}[^\-]$#u", message="name.wrong")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     * @Assert\NotBlank(message="surname.blank")
     * @Assert\Regex( pattern="#^(?!-)[\p{L}- ]{2,20}[^\-]$#u", message="surname.wrong")
     */
    private $surname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date")
     * @Assert\Date()
     * @BirthdayCheck()
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     * @Assert\Country()
     */
    private $country;

    /**
     * @var bool
     *
     * @ORM\Column(name="reduced", type="boolean")
     */
    private $reduced = false;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;


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
     * Set name
     *
     * @param string $name
     *
     * @return Visitor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return Visitor
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return Visitor
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Visitor
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set reduced
     *
     * @param boolean $reduced
     *
     * @return Visitor
     */
    public function setReduced($reduced)
    {
        $this->reduced = $reduced;

        return $this;
    }

    /**
     * Get reduced
     *
     * @return bool
     */
    public function getReduced()
    {
        return $this->reduced;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Visitor
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set ticketOrder
     *
     * @param \Louvre\ShopBundle\Entity\TicketOrder $ticketOrder
     *
     * @return Visitor
     */
    public function setTicketOrder(\Louvre\ShopBundle\Entity\TicketOrder $ticketOrder)
    {
        $this->ticketOrder = $ticketOrder;

        return $this;
    }

    /**
     * Get ticketOrder
     *
     * @return \Louvre\ShopBundle\Entity\TicketOrder
     */
    public function getTicketOrder()
    {
        return $this->ticketOrder;
    }

    /**
     * Récupére l'age du visiteur
     *
     * @return int
     */
    public function getAge()
    {
        return $this->getBirthday()->diff(new \DateTime())->y;
    }
}
