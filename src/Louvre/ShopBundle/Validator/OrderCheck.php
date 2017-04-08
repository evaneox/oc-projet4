<?php

namespace Louvre\ShopBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class OrderCheck extends Constraint
{
    public $messageIsTooOld         = "entry_date.is_too_old";
    public $messageIsTooFar         = "entry_date.is_too_far";
    public $messageIsTooLate        = "entry_date.is_too_late";
    public $messageIsClose          = "entry_date.is_close";
    public $messageIsFull           = "entry_date.is_full";
    public $messageTooMuchTickets   = "entry_date.too_much_ticket";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'louvre.order.check';
    }

}
