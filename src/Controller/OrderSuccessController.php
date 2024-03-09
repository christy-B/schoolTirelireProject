<?php

namespace App\Controller;

use App\CustomClass\Cart;
use App\CustomClass\Mail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderSuccessController extends AbstractController
{
    private $parameter;
    public function __construct(ParameterBagInterface $parameter)
    {
        $this->parameter = $parameter;
    }

    #[Route('/commande/success/{stripeSessionId}', name: 'app_order_validate')]
    public function index(EntityManagerInterface $manager, Cart $cart, $stripeSessionId): Response
    {
        $order = $manager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if ($order->getState() == 0) {
            $order->setState(1);
            $manager->flush();
        }

        //send email
        $mail = new Mail($this->parameter);
        $content = "Bonjour " . $order->getUser()->getFirstname() . "<br/><br/>
        merci pour votre commande<br/>
        Votre commande n° " . $order->getReference() . " est bien validée";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'commande chez la Boutique Tirelire', $content);

        //vider le panier
        $cart->remove();
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
