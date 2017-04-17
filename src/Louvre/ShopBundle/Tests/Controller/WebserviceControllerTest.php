<?php

namespace Louvre\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebserviceControllerTest extends WebTestCase
{
    /**
 * Vérification de la réponse obtenu pour une date ou le musée n'a plus de billets disponibles
 */
    public function testCapacityFullAction()
    {
        $client = static::createClient();

        // Lancement de la requete de type POST,
        // le nombre de billets définit est supérieur à la capacité du musée
        $crawler = $client->request(
            'POST',
            '/fr/webservice/booking/quota',
            array('date'      => '2017-01-01', 'tickets'   => '1001'),
            array(),
            array('HTTP_X-Requested-With' => 'XMLHttpRequest')
        );

        // Le serveur a bien répondu
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // La réponse obtenue est de type Json
        $this->assertTrue( $client->getResponse()->headers->contains( 'Content-Type', 'application/json'));

        // Plus de billets disponible
        $this->assertContains('{"availability":false}', $client->getResponse()->getContent());
    }

    /**
     * Vérification de la réponse obtenu pour une date ou le musée
     * a des billets disponibles supérieur à la limite d'achat de billets
     */
    public function testCapacityNotFullMoreThanBuyingLimitAction()
    {
        $client = static::createClient();

        // Lancement de la requete de type POST,
        // le nombre de billets définit n'est pas supérieur à la capacité du musée
        $crawler = $client->request(
            'POST',
            '/fr/webservice/booking/quota',
            array('date'      => '2017-01-01', 'tickets'   => '500'),
            array(),
            array('HTTP_X-Requested-With' => 'XMLHttpRequest')
        );

        // Le serveur a bien répondu
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // La réponse obtenue est de type Json
        $this->assertTrue( $client->getResponse()->headers->contains( 'Content-Type', 'application/json'));

        // Des billets sont bien disponibles,
        // de plus le nombre de billet que l'on peut acheter
        // à bien été fixer au plafond d'achat max malgrés le fait de vouloir acheter 500 billets
        $this->assertContains('{"availability":true,"remaining_purchase_item":10}', $client->getResponse()->getContent());
    }
}