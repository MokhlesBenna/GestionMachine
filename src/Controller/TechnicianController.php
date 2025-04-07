<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Notification;
use App\Entity\Machine;
use App\Entity\Technicien;
use App\Repository\MachineRepository;
use App\Repository\PanneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/technicien')]
class TechnicianController extends AbstractController
{
    #[Route('', name: 'technicien_dashboard')]
    public function dashboard(PanneRepository $panneRepository, MachineRepository $machineRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Technicien) {
            throw $this->createAccessDeniedException();
        }

        $totalPannes = $panneRepository->count(['technicien' => $user]);
        $totalActiveMachines = $machineRepository->count(['status' => 'active']);

        return $this->render('technicien/dashboard.html.twig', [
            'total_pannes_assigned' => $totalPannes,
            'total_active_machines' => $totalActiveMachines,
        ]);
    }
    #[Route('/technicien/search-machine', name: 'technicien_search_machine')]
    public function searchMachine(Request $request, MachineRepository $machineRepository): JsonResponse
    {
        $query = $request->query->get('q', '');
        $machines = $machineRepository->createQueryBuilder('m')
            ->where('m.nom LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    
        $data = array_map(fn($machine) => [
            'nom' => $machine->getNom(),
            'status' => $machine->getStatus(),
        ], $machines);
    
        return new JsonResponse($data);
    }
    
    #[Route('/machines', name: 'technicien_machine_list')]
    public function machineList(MachineRepository $machineRepository): Response
    {
        return $this->render('technicien/machine_list.html.twig', [
            'machines' => $machineRepository->findAll(),
        ]);
    }

    #[Route('/machine/{id}/toggle-status', name: 'technicien_machine_toggle_status')]
    public function toggleMachineStatus(Machine $machine, EntityManagerInterface $em): Response
    {
        $newStatus = $machine->getStatus() === 'active' ? 'inactive' : 'active';
        $machine->setStatus($newStatus);

        $notification = new Notification();
        $notification->setMessage(sprintf(
            'Le technicien %s a changé le statut de la machine "%s" en "%s".',
            $this->getUser()->getNom(),
            $machine->getNom(),
            $newStatus
        ));
        $notification->setTargetRole('ROLE_ADMIN');

        $em->persist($notification);
        $em->flush();

        $this->addFlash('success', "Le statut de la machine a été mis à jour en « $newStatus »." );

        return $this->redirectToRoute('technicien_machine_list');
    }

    #[Route('/profil', name: 'technicien_profile')]
    public function profile(): Response
    {
        return $this->render('technicien/technicianProfile.html.twig');
    }

    #[Route('/profil/update-image', name: 'technicien_update_profile_image', methods: ['POST'])]
    public function updateProfileImage(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Technicien $technician */
        $technician = $this->getUser();

        $file = $request->files->get('profileImage');

        if ($file) {
            $filename = uniqid('profile_', true) . '.' . $file->guessExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/uploads', $filename);

            $technician->setProfileImage($filename);
            $em->flush();

            $this->addFlash('success', 'Photo de profil mise à jour avec succès.');
        }

        return $this->redirectToRoute('technicien_profile');
    }

    #[Route('/profil/change-password', name: 'technicien_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Technicien $technician */
        $technician = $this->getUser();

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');

            if (!empty($newPassword) && $newPassword === $confirmPassword) {
                // NOTE: Implement password hashing before production!
                $technician->setPassword($newPassword);
                $em->flush();

                $this->addFlash('success', 'Mot de passe mis à jour avec succès.');
                return $this->redirectToRoute('technicien_profile');
            }

            $this->addFlash('danger', 'Les mots de passe ne correspondent pas ou sont vides.');
        }

        return $this->render('technicien/change_password.html.twig');
    }
}
