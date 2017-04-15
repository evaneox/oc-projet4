<?php

namespace Louvre\ShopBundle\Mailer;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Louvre\ShopBundle\Entity\TicketOrder;

class Mailer
{
    protected $mailer;
    private $translator;
    private $templating;
    private $from;
    private $reply;
    private $name;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating ,TranslatorInterface $translator, $from, $reply, $name)
    {
        $this->mailer       = $mailer;
        $this->templating   = $templating;
        $this->translator   = $translator;
        $this->from         = $from;
        $this->reply        = $reply;
        $this->name         = $name;
    }

    /**
     * Envoi d'un email
     *
     * @param $to
     * @param $subject
     * @param $body
     */
    public function sendMail($to, $subject, $body)
    {
        $mail = \Swift_Message::newInstance();
        $mail
            ->setFrom(array($this->from => $this->name))
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setReplyTo(array($this->reply => $this->name))
            ->setContentType('text/html')
        ;
        $this->mailer->send($mail);
    }

    /**
     * Construction d'un email pour l'envoi des tickets
     *
     * @param TicketOrder $order
     */
    public function sendTicket(TicketOrder $order)
    {
        $to         = $order->getEmail();
        $subject    = $this->translator->trans('email.complete.subject');
        $body       = $this->templating->render('LouvreShopBundle:Mail:ticket.html.twig', array('order' => $order));
        $this->sendMail($to, $subject, $body);

    }

    /**
     * Construction d'un email pour le renvoi des tickets perdu
     *
     * @param TicketOrder $order
     */
    public function resendTicket(TicketOrder $order)
    {
        $to         = $order->getEmail();
        $subject    = $this->translator->trans('email.lost.subject');
        $body       = $this->templating->render('LouvreShopBundle:Mail:ticket_lost.html.twig', array('order' => $order));
        $this->sendMail($to, $subject, $body);

    }


}