<?php

namespace Louvre\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    /**
     *  On vérifie que la page d'accueil du site est valide
     */
    public function testHomePageResponse()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // réponse valide
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
 *  La page de paiement n'est pas disponible sans avoir de commande en cours,
 *  On vérifie que la page de paiement est bien redirigé
 */
    public function testPaymentPageResponseWithoutRedirect()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/order/payment');

        // réponse présente bien une redirection
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    /**
     *  La page de paiement n'est pas disponible sans avoir de commande en cours,
     *  On vérifie que la page de paiement redirige bien vers une page valide
     */
    public function testPaymentPageResponseWithRedirect()
    {
        $client = static::createClient();
        $client->request('GET', '/fr/order/payment');
        $client->followRedirect();

        // redirection vers une page valide
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     *  La page de confirmation n'est pas disponible sans avoir de commande en cours,
     *  On vérifie que la page de confirmation est bien redirigé
     */
    public function testConfirmationPageResponseWithoutRedirect()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/fr/order/confirmation');

        // réponse présente bien une redirection
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    /**
     *  La page de confirmation n'est pas disponible sans avoir de commande en cours,
     *  On vérifie que la page de confirmation redirige bien vers une page valide
     */
    public function testConfirmationPageResponseWithRedirect()
    {
        $client = static::createClient();
        $client->request('GET', '/fr/order/confirmation');
        $client->followRedirect();

        // redirection vers une page valide
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}