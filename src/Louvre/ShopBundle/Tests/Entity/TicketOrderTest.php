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

        // Creation d'une commande
        $order = new TicketOrder();
        $order->setEmail('EXEMPLE@TLD.COM');
        $order->setFullDay(false);
        $order->setEntryDate(new \DateTime());
        $order->setCode('SUPERCODE123456');


        // Email a bien été mit en minuscule et identique à celui injecter par le setter
        $this->assertEquals('exemple@tld.com', $order->getEmail());

        // Identique à celui injecter par le setter
        $this->assertEquals(false, $order->getFullDay());

        // On vérifie qu'on récupére bien une instance de datetime
        $this->assertInstanceOf(DateTime::class, $order->getCreatedDate());

        // Code de vérification a bien été mit en minuscule et identique à celui injecter par le setter
        $this->assertEquals('supercode123456', $order->getCode());
    }
}

