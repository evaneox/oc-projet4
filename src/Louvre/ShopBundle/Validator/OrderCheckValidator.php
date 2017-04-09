<?php

namespace Louvre\ShopBundle\Validator;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;



class OrderCheckValidator extends ConstraintValidator
{
    protected $em;
    private $maxPurchaseItem;
    private $container;

    public function __construct(EntityManagerInterface $em, $container, $maxPurchaseItem)
    {
        $this->em               = $em;
        $this->container        = $container;
        $this->maxPurchaseItem  = $maxPurchaseItem;
    }

    public function validate($order, Constraint $constraint)
    {
        if(!is_null($order->getEntryDate())){

            // On vérifie si la date d'entrée n'est pas déja passé
            $limitDate = new \DateTime('0:00:00');
            if($order->getEntryDate() < $limitDate){
                $this->context->buildViolation($constraint->messageIsTooOld)->addViolation();
            }

            // On ne peut réserver que pendant 1ans
            $limitDate->modify('+1 year');
            if($order->getEntryDate() > $limitDate){
                $this->context->buildViolation($constraint->messageIsTooFar)->addViolation();
            }

            // On ne peut commander de ticket "Journée" une fois 14:00 passé
            $now  = new \DateTime('NOW');
            $date = $order->getEntryDate();

            if( ($now->format('Y-m-d') == $date->format('Y-m-d')) AND ($now->format('H') > 14) AND $order->getFullDay() ){
                $this->context->buildViolation($constraint->messageIsTooLate)->addViolation();
            }

            // On ne peut pas commander le mardi, dimanche ainsi que les 1er mai, 1er novembre et 25 décembre
            if($date->format('N') == 2 OR $date->format('N') == 7 OR $date->format('m-d') == '05-01' OR $date->format('m-d') == '11-01' OR $date->format('m-d') == '12-25'){
                $this->context->buildViolation($constraint->messageIsClose)->addViolation();
            }

            // 1. On vérifie que le nombre de billets n'excéde pas la limite autorisé
            // 2. On vérifie si le musée n'est pas complet pour cette date
            $numberOfTickets = $order->getCountVisitors();
            if($numberOfTickets <= $this->maxPurchaseItem){

                if(!$this->container->get('louvre_shop.webservice')->checkCapacity($date, $numberOfTickets)){
                    $this->context->buildViolation($constraint->messageIsFull)->addViolation();
                }

            }else{
                $this->context->buildViolation($constraint->messageTooMuchTickets)->addViolation();
            }
        }
    }
}
