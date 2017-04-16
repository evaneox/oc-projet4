<?php

namespace Louvre\ShopBundle\Tests\Entity;

use Louvre\ShopBundle\Entity\TicketOrder;
use DateTime;

class TicketsOrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test des getters et setters
     */
    public function testGettersAndSetters()
    {
        $order = new TicketOrder();

        /*******************
         * Hydratation d'une commande
         */
        $order->setEmail('EXEMPLE@TLD.COM');
        $order->setFullDay(false);
        $order->setEntryDate(new \DateTime());
        $order->setCode('SUPERCODE123456');

        /*******************
         * Comparaison des résulats
         */
        $this->assertEquals('exemple@tld.com', $order->getEmail());         // Résulat forcé en minuscule
        $this->assertEquals(false, $order->getFullDay());
        $this->assertInstanceOf(DateTime::class, $order->getCreatedDate()); // On vérifie qu'on récupére bien une instance de datetime
        $this->assertEquals('supercode123456', $order->getCode());          // Résulat forcé en minuscule
    }
}

