<?php

namespace Louvre\ShopBundle\Tests\Entity;

use Louvre\ShopBundle\Entity\Visitor;

class VisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test des getters et setters
     */
    public function testGettersAndSetters()
    {
        $visitor = new Visitor();

        $birthday = new \DateTime('1985-07-18');

        /*******************
         * Hydratation d'un visiteur
         */
        $visitor->setname('Zozor');
        $visitor->setSurname('Super');
        $visitor->setBirthday($birthday);
        $visitor->setCountry('FR');
        $visitor->setReduced(true);
        $visitor->setPrice(10);

        /*******************
         * Comparaison des résulats
         */
        $this->assertEquals('zozor', $visitor->getName());          // Résulat forcé en minuscule
        $this->assertEquals('super', $visitor->getSurname());       // Résulat forcé en minuscule
        $this->assertEquals($birthday, $visitor->getBirthday());
        $this->assertEquals(true, $visitor->getReduced());
        $this->assertEquals(10, $visitor->getPrice());
        $this->assertEquals($visitor->getBirthday()->diff(new \DateTime())->y, $visitor->getAge()); // On vérifie que le calcule de l'age est correcte.
    }
}

