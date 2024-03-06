<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccountOrderController extends AbstractController
{
    private $manager;
    public function __construct( EntityManagerInterface $manager) {
        $this->manager = $manager;
    }
    #[Route('/compte/mes-commande', name: 'app_account_order')]
    public function index(): Response
    {
        $orders = $this->manager->getRepository(Order::class)->findSuccessOrder($this->getUser());
        return $this->render('account/order.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/compte/mes-commande/{reference}', name: 'app_account_order_show')]
    public function show($reference): Response
    {
        $order = $this->manager->getRepository(Order::class)->findOneByReference($reference);
        if (!$order || $order->getUser() != $this->getUser()) {
            return$this->redirectToRoute('app_account_order');
        }
        return $this->render('account/order_show.html.twig', [
            'order' => $order
        ]);
    }
}
