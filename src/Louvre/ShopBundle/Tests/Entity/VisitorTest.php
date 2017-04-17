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

        $birthday = new \DateTime('1985-07-18');

        // Création d'un visiteur
        $visitor = new Visitor();
        $visitor->setname('Zozor');
        $visitor->setSurname('Super');
        $visitor->setBirthday($birthday);
        $visitor->setCountry('FR');
        $visitor->setReduced(true);
        $visitor->setPrice(10);

        // prénom a bien été mit en minuscule et identique à celui injecter par le setter
        $this->assertEquals('zozor', $visitor->getName());

        // nom a bien été mit en minuscule et identique à celui injecter par le setter
        $this->assertEquals('super', $visitor->getSurname());

        // identque à celui injecter par le setter
        $this->assertEquals($birthday, $visitor->getBirthday());

        // identque à celui injecter par le setter
        $this->assertEquals(true, $visitor->getReduced());

        // identque à celui injecter par le setter
        $this->assertEquals(10, $visitor->getPrice());

        // On vérifie que le calcul de l'age est correcte.
        $this->assertEquals($visitor->getBirthday()->diff(new \DateTime())->y, $visitor->getAge());
    }
}

