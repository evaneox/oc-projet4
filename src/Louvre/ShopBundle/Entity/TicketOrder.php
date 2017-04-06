<?php

namespace Louvre\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * TicketOrder
 *
 * @ORM\Table(name="ticket_order")
 * @ORM\Entity(repositoryClass="Louvre\ShopBundle\Repository\TicketOrderRepository")
 */
class TicketOrder
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
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="fullDay", type="boolean")
     */
    private $fullDay = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate", type="datetime")
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="entryDate", type="date")
     * @Assert\NotBlank(message="entry_date.blank")
     */
    private $entryDate;

    /**
     * @var int
     *
     * @ORM\OneToMany(targetEntity="Louvre\ShopBundle\Entity\Visitor", mappedBy="ticketOrder", cascade={"persist"})
     * @Assert\Valid()
     */
    private $visitors;

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->createdDate  = new \DateTime();
        $this->code         = strtolower(uniqid('LOU'));
        $this->visitors     = new ArrayCollection();

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
     * Set code
     *
     * @param string $code
     *
     * @return TicketOrder
     */
    public function setCode($code)
    {
        $this->code = strtolower($code);

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return TicketOrder
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
     * Set fullDay
     *
     * @param boolean $fullDay
     *
     * @return TicketOrder
     */
    public function setFullDay($fullDay)
    {
        $this->fullDay = $fullDay;

        return $this;
    }

    /**
     * Get fullDay
     *
     * @return bool
     */
    public function getFullDay()
    {
        return $this->fullDay;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return TicketOrder
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set entryDate
     *
     * @param \DateTime $entryDate
     *
     * @return TicketOrder
     */
    public function setEntryDate($entryDate)
    {
        $this->entryDate = $entryDate;

        return $this;
    }

    /**
     * Get entryDate
     *
     * @return \DateTime
     */
    public function getEntryDate()
    {
        return $this->entryDate;
    }

    /**
     * Add visitor
     *
     * @param \Louvre\ShopBundle\Entity\Visitor $visitor
     *
     * @return TicketOrder
     */
    public function addVisitor(\Louvre\ShopBundle\Entity\Visitor $visitor)
    {
        $this->visitors[] = $visitor;
        $visitor->setTicketOrder($this);
        return $this;
    }

    /**
     * Remove visitor
     *
     * @param \Louvre\ShopBundle\Entity\Visitor $visitor
     */
    public function removeVisitor(\Louvre\ShopBundle\Entity\Visitor $visitor)
    {
        $this->visitors->removeElement($visitor);
    }

    /**
     * Get visitors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVisitors()
    {
        return $this->visitors;
    }
}
