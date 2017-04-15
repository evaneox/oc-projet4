<?php

namespace Louvre\ShopBundle\Payment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Definition\Exception\Exception;
use Louvre\ShopBundle\Entity\TicketOrder;
use Doctrine\ORM\EntityManager;




class Payment
{
    protected $em;

    public function __construct(EntityManager $entityManager, $stripe_key)
    {
        $this->em   = $entityManager;
        \Stripe\Stripe::setApiKey($stripe_key);
    }

    /**
     * Paiement de la commande
     *
     * @param TicketOrder $order
     * @param Request $request
     * @return bool
     */
    public function sendingPayment(TicketOrder $order, request $request){

        // On vérifie que la commande et l'adresse email est valide
        if(is_null($order) || !filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)){
            return false;
        }

        // On vérifie si le paiement a été accepté.
        // Si le paiement n'est pas valide pour notre cas, on retourne false
        // afin d'afficher un message global à l'utilisateur.
        try {
            \Stripe\Charge::create(array(
                "amount"        => $order->getTotalPrice() * 100 ,
                "currency"      => "eur",
                "description"   => "Musée du Louvre - Paiement des billets",
                "source"        => $request->get('stripeToken'),
            ));

        } catch(\Stripe\Error\Card $e) {
            // la carte n'est pas valide
            return false;

        } catch (\Stripe\Error\InvalidRequest $e) {
            // Des paramètres non valides ont été envoyé à l'API Stripe
            return false;

        } catch (\Stripe\Error\Authentication $e) {
            // L'authentification avec Stripe a échoué
            return false;

        } catch (\Stripe\Error\ApiConnection $e) {
            // La communication réseau avec Stripe a échoué
            return false;

        } catch (\Stripe\Error\Base $e) {
            // Affiche une erreur générique à l'utilisateur
            return false;

        } catch (Exception $e) {
            // Une autre chose s'est produite, totalement sans lien avec Stripe
            return false;
        }

        return true;

    }
}