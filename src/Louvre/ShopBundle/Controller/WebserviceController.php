<?php

namespace Louvre\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WebserviceController extends Controller
{
    /**
     * Vérification de la capacité du musée en fonction de la date de la visite et du nombre de tickets désiré
     *
     * @param Request $request
     * @return mixed
     */
    public function quotaAction(Request $request){

        if ($request->isXmlHttpRequest()) {
            $date                   = new \DateTime($request->get('date'));
            $tickets                = (int) $request->get('tickets');

            if($this->get('louvre_shop.webservice')->checkCapacity($date, $tickets)){
                $remainingPurchaseItemOnDate = $this->get('louvre_shop.webservice')->getRemainingTickets($date, $tickets);
                $response = ['availability' => true, 'remaining_purchase_item' => $remainingPurchaseItemOnDate ];
            }else{
                $response =  ['availability' => false];
            }

        }else{
            $response = array(
                'errorCode' => 400,
                'errorMsg'  => 'Bad Request'
            );
        }
        return new JsonResponse($response);
    }
}