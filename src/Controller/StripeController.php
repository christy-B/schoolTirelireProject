<?php

namespace App\Controller;

use App\CustomClass\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends AbstractController
{

    #[Route('/commande/create/session', name: 'app_stripe_create_session')]
    public function index(Request $request, Cart $cart)
    {
        $product_for_stripe = [];
        $YOUR_DOMAIN = $_ENV["DOMAIN_NAME"];

        foreach ($cart->getFull() as $product) {
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' =>[$YOUR_DOMAIN."/assets/illustration/".$product['product']->getIllustration()],
                    ],
                ],
                'quantity' => $product['quantity'],
            ];
        }
        

        Stripe::setApiKey($_ENV["STRIPE_SECRET"]);

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                $product_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);
        return $this->json(['id' => $checkout_session->id]);
    }
}
