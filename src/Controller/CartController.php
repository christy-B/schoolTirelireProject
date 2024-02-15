<?php

namespace App\Controller;

use App\CustomClass\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cart;
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(): Response
    {
        $cart = $this->cart->getFull();
        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'app_add_to_cart')]
    public function add(Cart $cart, $id): Response
    {

        $cart->add($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/supprimer/{id}', name: 'app_delete_to_cart')]
    public function remove(Cart $cart, $id): Response
    {

        $cart->delete($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/panier/diminuer/{id}', name: 'app_decrease_to_cart')]
    public function decrease(Cart $cart, $id): Response
    {

        $cart->decrease($id);
        return $this->redirectToRoute('app_cart');
    }
}
