<?php

namespace Louvre\ShopBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BirthdayCheck extends Constraint
{
    public $message                 = "birthday.wrong";

    public function validatedBy()
    {
        return 'louvre.birthday.check';
    }

}
