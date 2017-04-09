<?php

namespace Louvre\ShopBundle\Webservice;

use Doctrine\ORM\EntityManager;

class Webservice
{
    protected $em;
    private $maxCapacity;
    private $maxPurchaseItem;

    public function __construct(EntityManager $entityManager, $maxCapacity, $maxPurchaseItem)
    {
        $this->em           = $entityManager;
        $this->maxCapacity      = (int) $maxCapacity;
        $this->maxPurchaseItem  = (int) $maxPurchaseItem;
    }

    /**
     * Vérifie si le musée peut encore vendre x tickets pour x date
     *
     * @param \DateTime $date
     * @param $tickets
     * @return bool
     */
    public function checkCapacity(\DateTime $date, $tickets)
    {
        $totalTicketForThisDate = $this->getNumberTicketFor($date);

        return ( ($totalTicketForThisDate + $tickets) > $this->maxCapacity ) ? false : true;
    }

    /**
     * Retourne le nombre de tickets restant à hauteur du nombre maximum autorisé
     *
     * @param \DateTime $date
     * @param $tickets
     * @return int
     */
    public function getRemainingTickets(\DateTime $date, $tickets)
    {
        $totalTicketForThisDate = $this->getNumberTicketFor($date);

        return (int) ( ($this->maxCapacity - $totalTicketForThisDate) < $this->maxPurchaseItem ) ? ($this->maxCapacity - $totalTicketForThisDate) : $this->maxPurchaseItem;
    }

    /**
     * récupére le nombre de ticket pour une date
     *
     * @param \DateTime $date
     * @return mixed
     */
    private function getNumberTicketFor(\DateTime $date)
    {
        return $this->em->getRepository('LouvreShopBundle:TicketOrder')->getTicketsFor($date);
    }
}

