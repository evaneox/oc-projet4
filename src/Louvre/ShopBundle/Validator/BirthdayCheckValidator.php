<?php

namespace Louvre\ShopBundle\Validator;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;



class BirthdayCheckValidator extends ConstraintValidator
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em  = $em;
    }

    public function validate($date, Constraint $constraint)
    {
        $now = new \DateTime('now');
        if(is_null($date) || ($date > $now )){
            $this->context->addViolation($constraint->message);
        }
    }
}
