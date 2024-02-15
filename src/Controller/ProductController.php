<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/nos-produit', name: 'app_product')]
    public function index(): Response
    {
        $products = $this->manager->getRepository(Product::class)->findAll();
    
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/produit/{slug}', name: 'app_product_slug')]
    public function show($slug): Response
    {
        $product = $this->manager->getRepository(Product::class)->findOneBySlug($slug);
         if (!$product) {
            return $this->redirectToRoute('app_product');
         }
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
