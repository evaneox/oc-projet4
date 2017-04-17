<?php
namespace Louvre\ShopBundle\Tests\Form;

use Louvre\ShopBundle\Entity\TicketOrder;
use Louvre\ShopBundle\Entity\Visitor;
use Louvre\ShopBundle\Form\Type\TicketOrderType;

use Symfony\Component\Form\Test\TypeTestCase;

class TicketOrderTypeTest extends TypeTestCase
{
    /**
     * Vérifie si les données du formulaire sont bien validées
     */
    public function testSubmittedData()
    {
        $birthday = new \DateTime('1985-07-18');

        //Creation d'n visiteur
        $visitor = new Visitor();
        $visitor->setSurname('Super');
        $visitor->setBirthday($birthday);
        $visitor->setCountry('FR');
        $visitor->setReduced(true);

        $data = [
            'entryDate' => new \DateTime(),
            'fullDay'   => true,
            'visitors'  => $visitor
        ];

        $form = $this->factory->create(VisitorType::class);
        $object = TicketOrder::fromArray($data);
        $form->submit($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());
    }
}

