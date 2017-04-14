<?php

namespace Louvre\ShopBundle\Mailer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Louvre\ShopBundle\Entity\TicketOrder;

class Mailer
{
    protected $mailer;
    private $translator;
    private $templating;
    private $from;
    private $reply;
    private $name;

    public function __construct(ContainerInterface $container, $from, $reply, $name)
    {
        $this->mailer       = $container->get('mailer');
        $this->translator   = $container->get('translator');
        $this->templating   = $container->get('templating');
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

}