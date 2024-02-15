<?php

namespace App\CustomClass;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    protected $session;
    protected $manager;
    public function __construct(RequestStack $requestStack, EntityManagerInterface $manager)
    {
        $this->session = $requestStack->getSession();
        $this->manager = $manager;
    }

    //ajouter un produit
    public function add($id)
    {
        $cart = $this->session->get('cart', []);
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else $cart[$id] = 1;

        $this->session->set('cart', $cart);
    }

    //diminuer un produit
    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);
        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else unset($cart[$id]);

        $this->session->set('cart', $cart);
    }

    //obtenir un produit
    public function get()
    {
        return $this->session->get('cart');
    }

    //supprimer un produit
    public function delete($id)
    {
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);
        return $this->session->set('cart', $cart);
    }

    //obtenir tout les produits du panier
    public function getFull()
    {
        $allCart = [];
       
        if ($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $produit = $this->manager->getRepository(Product::class)->findOneById($id);
                if (!$produit) {
                    $this->delete($id);
                    continue;
                }
                $allCart[] = [
                    'product' => $produit,
                    'quantity' => $quantity
                ];
            }
        }

        return $allCart;
    }

    //supprimer tout le panier
    public function remove()
    {
        return $this->session->remove('cart');
    }
}
