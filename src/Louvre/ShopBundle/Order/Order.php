<?php

namespace Louvre\ShopBundle\Order;

use Doctrine\ORM\EntityManager;
use Louvre\ShopBundle\Entity\TicketOrder;

class Order
{
    protected $em;
    private $visitors;

    const FREE          = 'FREE';
    const CHILDREN      = 'CHILDREN';
    const SENIORS       = 'SENIORS';
    const NORMAL        = 'NORMAL';

    public function __construct(EntityManager $entityManager)
    {
        $this->em           = $entityManager;
    }

    public function create(TicketOrder $order)
    {
        $this->visitors = $order->getVisitors();
        $now            = new \DateTime();

        // Pour chaque visiteurs on va déterminer le prix de leurs billet en fonction
        // 1.de l'age du visiteurs
        // 2.de la réduction
        // 3. du type de billet
        foreach($this->visitors as $visitor){

            $age = $visitor->getAge();

            // Determine la categorie de prix en fonction de l'age
            if($age < 4){
                $priceType = self::FREE;
            }elseif($age >= 4 AND $age < 12){
                $priceType = self::CHILDREN;
            }elseif($age >= 60){
                $priceType = self::SENIORS;
            }else{
                $priceType = self::NORMAL;
            }
            /*var_dump($priceType);*/

            $Price = $this->em->getRepository('LouvreShopBundle:Pricing')->findOneBy(['name' => $priceType])->getValue();

            /* Information à demander
            if($visitor->getReduced() AND ($priceType == self::NORMAL OR $priceType == self::SENIORS))
            {
                $reducedPrice = $this->pricingRepo->findOneBy(['name' => $priceType])->getValue();
                var_dump($reducedPrice);
            }*/

        }


    }
}
