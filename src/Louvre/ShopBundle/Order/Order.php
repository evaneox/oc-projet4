<?php

namespace Louvre\ShopBundle\Order;

use Doctrine\ORM\EntityManager;
use Louvre\ShopBundle\Entity\TicketOrder;
use Symfony\Component\HttpFoundation\RequestStack;
use Louvre\ShopBundle\Mailer\Mailer;

class Order
{
    protected $em;
    private $visitors;
    private $PricingRepository;
    private $request;

    const FREE          = 'FREE';
    const CHILDREN      = 'CHILDREN';
    const SENIORS       = 'SENIORS';
    const NORMAL        = 'NORMAL';
    const REDUCED       = 'REDUCED';

    public function __construct(EntityManager $entityManager, RequestStack $requestStack, Mailer $mailer)
    {
        $this->em                   = $entityManager;
        $this->PricingRepository    = $this->em->getRepository('LouvreShopBundle:Pricing');
        $this->request              = $requestStack->getCurrentRequest();
        $this->mailer               = $mailer;
    }

    /**
     * Création de la commande
     *
     * @param TicketOrder $order
     */
    public function create(TicketOrder $order)
    {
        $this->visitors = $order->getVisitors();
        $now            = new \DateTime();

        // Pour chaque visiteurs on va déterminer le prix de leurs billet en fonction
        // 1.de l'age du visiteurs
        // 2.de la réduction
        // 3. du type de billet
        foreach($this->visitors as $visitor) {

            $age = $visitor->getAge();

            // Determine le tarif du billet en fonction de l'age
            if($age < 4) {
                $priceType = self::FREE;
            } elseif($age >= 4 && $age < 12) {
                $priceType = self::CHILDREN;
            } elseif($age >= 60) {
                $priceType = self::SENIORS;
            } else {
                $priceType = self::NORMAL;
            }

            $Price = $this->PricingRepository->findOneBy(['name' => $priceType])->getValue();

            // La réduction ne s'applique que pour les billets journée avec tarif normal
            if($visitor->getReduced()){

                if($order->getFullDay() && ($priceType == self::NORMAL || $priceType == self::SENIORS ))
                {
                    $reducedPrice = $this->PricingRepository->findOneBy(['name' => self::REDUCED])->getValue();
                    $Price = ($Price - $reducedPrice);
                    $visitor->setReduced(true);
                    $priceType = self::REDUCED;
                }else
                {
                    $visitor->setReduced(false);
                }
            }
            $visitor->setType($priceType);
            $visitor->setPrice($Price);
        }

        // On passe la commande en session
        $this->request->getSession()->set('order', $order);
    }

    /**
     * Finalisation et enregistrement de la commande finale
     *
     * @param TicketOrder $order
     */
    public function save(TicketOrder $order){

        // On vérifie que la commande est valide
        if(!is_null($order)){
            $this->visitors = $order->getVisitors();

            // 1. On met à jour l'adresse email utilisé pour le paiement de la commande
            $order->setEmail($this->request->get('email'));

            // 2. on lie les visiteurs à la commande
            foreach($this->visitors as $visitor) {
                $visitor->setTicketOrder($order);
                $this->em->persist($visitor);
            }

            // 3. enregistrement en base de données
            $this->em->persist($order);
            $this->em->flush();

            // 4. On envoie contenant les billets
            $this->mailer->sendTicket($order);

        }
    }

    public function recovery(){

        // On commence par récupérer le code de réservation
        $code = strtoupper($this->request->get('order_identifier'));

        // On vérifie que le code n'est pas vide
        if(!empty($code)){

            // On recupére la commande lié au code
            $order = $this->em->getRepository('LouvreShopBundle:TicketOrder')->findOneBy(['code' => $code]);

            if(!is_null($order)){
                // on renvoi les billets par email
                $this->mailer->resendTicket($order);
                return true;
            }
        }

        return false;
    }
}
