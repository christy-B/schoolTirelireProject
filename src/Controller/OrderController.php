<?php

namespace App\Controller;

use App\CustomClass\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class OrderController extends AbstractController
{
    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/commande', name: 'app_order')]
    public function index(Cart $cart, Request $request): Response
    {
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('app_add_address');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }
    //
    #[Route('/commande/recapitulatif', name: 'app_order_recap', methods:"POST")]
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTimeImmutable();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $deliver_content = $delivery->getFirstName() . ' ' . $delivery->getLastName();
            $deliver_content .= '<br/>' . $delivery->getPhone();
            if ($delivery->getCompany()) {
                $deliver_content .= '<br/>' . $delivery->getCompany();
            }
            $deliver_content .= '<br/>' . $delivery->getAddress();
            $deliver_content .= '<br/>' . $delivery->getPostal() . ' ' . $delivery->getCity();
            $deliver_content .= '<br/>' . $delivery->getCountry();

            //enregistrer ma commande order
            $order = new Order();
            $reference = $date->format('daY').'-'.uniqid();
            $order->setReference($reference);
            $order->setCreatedAt($date);
            $order->setUser($this->getUser());
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($deliver_content);
            $order->setState(0);

            $this->manager->persist($order);

            //enregistrer mes produits orderDetails
            foreach ($cart->getFull() as $product) {
                $orderDetail = new OrderDetails();
                $orderDetail->setMyOrder($order);
                $orderDetail->setProduct($product['product']->getName());
                $orderDetail->setQuantity($product['quantity']);
                $orderDetail->setPrice($product['product']->getPrice());
                $orderDetail->setTotal($product['product']->getPrice() * $product['quantity']);
                $orderDetail->setState(0);
                $this->manager->persist($orderDetail);
            }
            
            $this->manager->flush();

            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $deliver_content,
                'reference' =>$order->getReference(),
                'stripe_key' => $_ENV["STRIPE_KEY"],
            ]);
        }

        return $this->redirectToRoute('app_cart');
        
    }
}
