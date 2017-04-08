<?php

namespace Louvre\ShopBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Louvre\ShopBundle\Entity\Pricing;

class LoadPrice implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $prices = array(
            'FREE'          => 0,
            'CHILDREN'      => 8,
            'NORMAL'        => 16,
            'REDUCED'       => 10,
            'SENIORS'       => 10
        );

        foreach($prices as $name => $value){
            $pricing = new Pricing();
            $pricing->setName($name);
            $pricing->setValue($value);
            $manager->persist($pricing);
        }
        $manager->flush();

    }
}