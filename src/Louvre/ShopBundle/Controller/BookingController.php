<?php

namespace Louvre\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Louvre\ShopBundle\Entity\TicketOrder;
use Louvre\ShopBundle\Form\TicketOrderType;

class BookingController extends Controller
{
    /**
     * Affichage de la Homepage [Commande- step1]
     *
     * @param Request $request
     * @return mixed
     */
    public function indexAction(Request $request){
        $ticketOrder = new TicketOrder();
        $form = $this->createForm(TicketOrderType::class, $ticketOrder);
        $form->handleRequest($request);

        // le visiteur a validé le formulaire
        if ($form->isSubmitted() && $form->isValid()){

        }else{
            $this->addFlash('error', 'problème');
            dump($form->getErrors());
        }
        return $this->render('LouvreShopBundle:Booking:first_step.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Affichage des mentions légales
     * @return mixed
     */
    public function mentionsViewAction(){
        return $this->render('LouvreShopBundle:Booking:mentions.html.twig');
    }
}