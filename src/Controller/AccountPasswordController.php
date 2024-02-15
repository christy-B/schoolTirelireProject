<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AccountPasswordController extends AbstractController
{

    #[Route('/compte/modifier-mon-mot-de-passe', name: 'app_account_password')]
    public function index(Request $request,UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $manager, Security $security): Response
    {
        $notification = null;

        $user = $security->getUser();

        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($user instanceof User) {
            if ($form->isSubmitted() && $form->isValid()) {
                $old_pwd = $form->get('old_password')->getData();
                if ($passwordHasher->isPasswordValid($user, $old_pwd)) {
                    $new_pwd = $form->get('new_password')->getData();
                    $user->setPassword($passwordHasher->hashPassword($user, $new_pwd));
                    //manager->persist($user); //pas besoin quand on fait une mise a jour
                    $manager->flush();
                    $notification = "Votre mot de passe a bien été mis à jour";
                } else $notification = "Votre mot de passe n'est pas le bon";
            }
        }
        
        return $this->render('account/password.html.twig',[
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
