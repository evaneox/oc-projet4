<?php

namespace Louvre\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WebserviceController extends Controller
{
    /**
     * Vérification de la capacité du musée en fonction de la date de la visite
     *
     * @param Request $request
     * @return mixed
     */
    public function quotaAction(Request $request){

        if ($request->isXmlHttpRequest()) {
            $date                   = new \DateTime($request->get('date'));
            $maxCapacity            = (int) $this->getParameter('max_capacity');
            $maxPurchaseItem        = (int) $this->getParameter('max_purchase_item');

            // Récupération du nombre de billets pour cette date
            $totalTicketForThisDate = $this->getDoctrine()->getRepository('LouvreShopBundle:TicketOrder')->getTicketsFor($date);

            // Si on ne peut vendre au moins 1 billets, on considére que le musée est complet
            // dans le cas contraire on retourne le nombre de billet de vente maximum à hauteur des billets restants
            $remainingPurchaseItemOnDate = ( ($maxCapacity - $totalTicketForThisDate) < $maxPurchaseItem ) ? ($maxCapacity - $totalTicketForThisDate) : $maxPurchaseItem;
            //$response = (($totalTicketForThisDate + 1) > $maxCapacity) ? ['availability' => false] : ['availability' => true, 'remaining_purchase_item' => $remainingPurchaseItemOnDate ];
            $response = ['availability' => true, 'remaining_purchase_item' => 15 ];
        }else{
            $response = array(
                'errorCode' => 400,
                'errorMsg'  => 'Bad Request'
            );
        }
        return new JsonResponse($response);
    }
}