<?php

namespace App\Controller;

use App\CustomClass\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{

    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $notification = null;

        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $search_email = $manager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$search_email) {
               
                $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
                $user->setRoles(['ROLE_USER']);
                $manager->persist($user);
                $manager->flush();

                //
                $mail = new Mail;
                $content = "Bonjour". $user->getFirstname()."<br>
Bienvenu dans votre boutique, nous somme ravi de vous compter parmi nous!";
                $mail->send($user->getEmail(), $user->getFirstname(), 'Bienvenu', $content);
                $notification = "vous etes bien inscrit";
            } else {
                $notification = "un compte est déja enregistrer avec cet email";
            }
            
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
