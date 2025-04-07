<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Technicien;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $userType = $data['user_type'];
            $email = $data['email'];
            $password = $data['password'];

            if ($userType === 'admin') {
                $user = new Admin();
                $user->setRoles(['ROLE_ADMIN']);
            } else {
                $user = new Technicien();
                $user->setRoles(['ROLE_TECHNICIEN']);
                $user->setNom((string) ($data['nom'] ?? ''));
                $user->setPrenom((string) ($data['prenom'] ?? ''));
                $user->setSpecialite((string) ($data['specialite'] ?? ''));
                $user->setTelephone((string) ($data['telephone'] ?? ''));
                $user->setLocalisation((string) ($data['localisation'] ?? ''));
            }

            $user->setEmail($email);
            $user->setPassword($password); // For dev only - plaintext password

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Account created! You can now log in.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
