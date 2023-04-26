<?php

namespace App\Controller\Admin\Site;

use App\Entity\Site;
use App\Entity\UserSite;
use App\Form\SiteType;
use App\Form\UserSiteType;
use App\Repository\PostRepository;
use App\Repository\SiteRepository;
use App\Repository\UserSiteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/sites')]
class AdminSiteController extends AbstractController
{
    #[Route('/', name: 'admin_site_index')]
    public function index(SiteRepository $siteRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $sites = $siteRepository->getAll();
        $pagination = $paginator->paginate(
            $sites,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('admin/site/index.html.twig', [
            'title' => 'Menedżer stron',
            'sites' => $pagination,
        ]);
    }

    #[Route('/{siteId}/show', name: 'admin_site_show')]
    public function show(int $siteId, SiteRepository $siteRepository, PostRepository $postRepository, UserSiteRepository $userSiteRepository, PaginatorInterface $paginator, Request $request): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        $requestQuery = $request->query;
        $posts = $postRepository->getListBySite($site, $requestQuery);
        $usersAssignedToThisSite = $userSiteRepository->findBy(['site' => $site]);
        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            9
        );
        $userSite = new UserSite();
        $userSite->setSite($site);
        $form = $this->createForm(UserSiteType::class, $userSite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userSiteRepository->save($userSite, true);

            return $this->redirectToRoute('admin_site_show', ['siteId' => $siteId]);
        }

        return $this->render('admin/site/show.html.twig', [
            'title' => $site->getName(),
            'site' => $site,
            'posts' => $pagination,
            'form' => $form,
            'userAssignedToThisSite' => $usersAssignedToThisSite
        ]);
    }

    #[Route('/create', name: 'admin_site_create')]
    public function new(Request $request, SiteRepository $siteRepository): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $siteRepository->save($site, true);

            return $this->redirectToRoute('admin_site_index');
        }

        return $this->render('defaults/new.html.twig', [
            'title' => 'Tworzenie strony',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{siteId}/edit', name: 'admin_site_edit')]
    public function edit(int $siteId, Request $request, SiteRepository $siteRepository): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $siteRepository->save($site, true);

            return $this->redirectToRoute('admin_site_index');
        }

        return $this->render('defaults/edit.html.twig', [
            'title' => 'Edycja strony',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{siteId}/deleteUserSite/{userSiteId}', name: 'admin_user_site_delete')]
    public function deleteUserSite(int $siteId, int $userSiteId, UserSiteRepository $userSiteRepository, SiteRepository $siteRepository): Response
    {
        if (!$siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$userSite = $userSiteRepository->find($userSiteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje użytkownik o takim id.');
        }
        $userSiteRepository->remove($userSite, true);

        return $this->redirectToRoute('admin_site_show', ['siteId' => $siteId]);
    }
}
