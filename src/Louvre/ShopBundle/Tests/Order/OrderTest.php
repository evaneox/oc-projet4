
<?php
/*
namespace Louvre\ShopBundle\Tests\Order;

use Louvre\ShopBundle\Order\Order;

use Louvre\ShopBundle\Entity\TicketOrder;
use Louvre\ShopBundle\Entity\Visitor;

class OrderTest extends \PHPUnit_Framework_TestCase
{

    public function testCountVisitorAndPricing()
    {
        // Création d'un premier visiteur type à tarif reduit > 12ans
        $visitor = new Visitor();

        $visitor->setname('Zozor');
        $visitor->setSurname('Super');
        $visitor->setBirthday(new \DateTime('1985-07-18'));
        $visitor->setCountry('FR');
        $visitor->setReduced(true);

        // Création d'un second visiteur type à tarif reduit < 12ans
        $visitor2 = new Visitor();

        $visitor2->setname('Zizir');
        $visitor2->setSurname('Extra');
        $visitor2->setBirthday(new \DateTime('2012-05-07'));
        $visitor2->setCountry('FR');
        $visitor2->setReduced(true);

        // Création d'une commande
        $order = new TicketOrder();

        $order->setEntryDate(new \DateTime('2017-05-04'));
        $order->setEmail('exemple@tld.com');

        // On ajoute nos 2 visiteurs pour la commande
        $order->addVisitor($visitor);
        $order->addVisitor($visitor2);

        // On créer notre manager et on demande à créer une commande
        // qui respecte nos différentes contraintes
        $orderManager = new Order();
        $orderManager->create($order);




    }
}*/

