<?php

namespace Louvre\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    /**
     *  Vérification de la réponse de la page de commande
     */
    public function testHomePageResponse()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     *  Vérification de la réponse de la page de commande
     */
    public function testPaymentResponse()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/order/payment');
        $this->assertEquals(304, $client->getResponse()->getStatusCode());
    }
}