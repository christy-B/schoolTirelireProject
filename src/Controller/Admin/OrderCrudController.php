<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\TextEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    private $manager;
    private $adminUrlGenerator;

    public function __construct(EntityManagerInterface $manager, AdminUrlGenerator $adminUrlGenerator )
    {
        $this->manager = $manager;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Preparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateLivraison = Action::new('updateLivraison', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateLivraison');
        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateLivraison)
            ->add('index', 'detail');
    }

    //ajouter une action
    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->manager->flush();
        $this->addFlash('notice', "<span style='color:green; margin:auto; font-weigth:bold'> la commande ".$order->getReference()." est bien en cours de preparation </span>");

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateLivraison(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->manager->flush();
        $this->addFlash('notice', "<span style='color:orange; margin:auto; font-weigth:bold'> la commande ".$order->getReference()." est bien en cours de livraison </span>");

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id'=>'desc']);
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt')->setLabel('Passé le'),
            TextField::new('user.fullName')->setLabel('Utilisateur'),
            TextEditorField::new('delivery', 'Adresse de livraison')->onlyOnDetail(),
            MoneyField::new('total')->setCurrency('EUR'),
            TextField::new('carrierName')->setLabel('transporteur'),
            MoneyField::new('carrierPrice', 'frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non Payé' => 0,
                'Payé' => 1,
                'Preparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'produit achété')->hideOnIndex()
        ];
    }
}
