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

        // On annule la commande précédente si l'utilisateur revient sur la page de réservation
        if($request->getSession()->has('order')){
            $this->get('louvre_shop.order')->delete($request->getSession()->get('order'));
        }

        $ticketOrder = new TicketOrder();
        $form = $this->createForm(TicketOrderType::class, $ticketOrder);
        $form->handleRequest($request);

        // Lorsque le visiteur à validé sa commande, on vérifie les éléments du formulaire
        if ($form->isSubmitted() && $form->isValid()){
            $this->get('louvre_shop.order')->create($ticketOrder);
            return $this->redirectToRoute('louvre_payment');
        }

        return $this->render('LouvreShopBundle:Booking:first_step.html.twig', array(
            'form'      => $form->createView()
        ));
    }

    /**
     * Affichage de la page de paiement [Commande - step2]
     *
     * @return mixed
     */
    public function orderPaymentAction(Request $request){

        // Seul les utilisateur ayant une commande en cours sont autorisés à continuer
        if(!$request->getSession()->has('order') OR is_null($request->getSession()->get('order'))){
            return $this->redirectToRoute('louvre_order');
        }

        // On récupére la commande en cours
        $order = $request->getSession()->get('order');

        // Lorsque le visiteur à validé son paiement
        if ($request->isMethod('POST')) {

            // On vérifie que le paiement a été validé
            if($this->get('louvre_shop.payment')->sendingPayment($order, $request)){

                // On peut finaliser la commande et rediriger vers la page de confirmation
                $this->get('louvre_shop.order')->save($order);
                //return $this->redirectToRoute('louvre_confirmation');
            }else{
                $request->getSession()->getFlashBag()->add('error', $this->get('translator')->trans('payment_fail'));
                return $this->redirectToRoute('louvre_payment');
            }
        }

        return $this->render('LouvreShopBundle:Booking:second_step.html.twig', array(
            'order'     => $order,
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