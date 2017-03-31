<?php

namespace Louvre\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    /*
     * Affiche de la page d'accueil qui représente aussi
     * la premiére etape de la commande
     */
    public function indexAction(){
        return $this->render('LouvreShopBundle:Booking:first_step.html.twig');
    }

    /*
     * Affiche des mentions légales
     */
    public function mentionsViewAction(){
        return $this->render('LouvreShopBundle:Booking:mentions.html.twig');
    }
}