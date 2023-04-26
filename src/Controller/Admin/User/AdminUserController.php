<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index')]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $userRepository->getAll();
        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('admin/user/index.html.twig', [
            'title' => 'Użytkownicy',
            'users' => $pagination,
        ]);
    }
    #[Route('/create', name: 'admin_user_create')]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $userRepository->save($user, true);
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('defaults/new.html.twig', [
            'title' => 'Dodawanie użytkownika',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{userId}/edit', name: 'admin_user_edit')]
    public function edit(int $userId, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if (!$user = $userRepository->find($userId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('plainPassword')->getData())) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            $userRepository->save($user, true);
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('defaults/edit.html.twig', [
            'title' => 'Edycja użytkownika',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{userId}/delete', name: 'admin_user_delete')]
    public function delete(int $userId, UserRepository $userRepository): Response
    {
        if (!$user = $userRepository->find($userId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        $userRepository->remove($user, true);

        return $this->redirectToRoute('admin_user_index', ['userId' => $userId]);
    }
}
