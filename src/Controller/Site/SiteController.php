<?php

namespace App\Controller\Site;

use App\Entity\PostTemplate;
use App\Form\PostTemplateType;
use App\Form\Seo\MetaOgType;
use App\Form\Seo\MetaTwitterType;
use App\Form\Seo\MetaType;
use App\Repository\PostRepository;
use App\Repository\PostTemplateRepository;
use App\Repository\SiteRepository;
use App\Repository\UserSiteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sites')]
class SiteController extends AbstractController
{
    #[Route('/', name: 'app_site_index')]
    public function index(SiteRepository $siteRepository, Security $security): Response
    {
        $sites = $siteRepository->getList($security->getUser());

        return $this->render('site/index.html.twig', [
            'title' => 'Twoje strony',
            'sites' => $sites,
        ]);
    }

    #[Route('/{siteId}', name: 'app_site_show')]
    public function show(int $siteId, SiteRepository $siteRepository, PostRepository $postRepository, UserSiteRepository $userSiteRepository, PaginatorInterface $paginator, Security $security, Request $request): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$userSiteRepository->findBy(['user' => $security->getUser(), 'site' => $site])) {
            throw $this->createNotFoundException('Nie masz dostępu do tej strony.');
        }
        $requestQuery = $request->query;
        $posts = $postRepository->getListBySite($site, $requestQuery);
        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('site/show.html.twig', [
            'title' => $site->getName(),
            'site' => $site,
            'posts' => $pagination,
        ]);
    }

    #[Route('/{siteId}/seo', name: 'app_site_seo_show')]
    public function seo(int $siteId, SiteRepository $siteRepository, UserSiteRepository $userSiteRepository, Security $security): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$userSiteRepository->findBy(['user' => $security->getUser(), 'site' => $site])) {
            throw $this->createNotFoundException('Nie masz dostępu do tej strony.');
        }

        return $this->render('site/seo.html.twig', [
            'title' => 'Zarządzanie SEO',
            'site' => $site,
            'back_link' => 'app_site_show'
        ]);
    }

    #[Route('/{siteId}/seo/edit', name: 'app_site_seo_edit')]
    public function seoEdit(int $siteId, SiteRepository $siteRepository, UserSiteRepository $userSiteRepository, Security $security, Request $request): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$userSiteRepository->findBy(['user' => $security->getUser(), 'site' => $site])) {
            throw $this->createNotFoundException('Nie masz dostępu do tej strony.');
        }
        $form = $this->createForm(MetaType::class, $site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $siteRepository->save($site, true);
            $this->addFlash('success', 'Pomyślnie zaktualizowano metadane.');

            return $this->redirectToRoute('app_site_seo_show', ['siteId' => $siteId]);
        }

        return $this->render('defaults/edit.html.twig', [
            'title' => 'Edycja metadanych',
            'form' => $form
        ]);
    }

    #[Route('/{siteId}/seo-og/edit', name: 'app_site_seo_og_edit')]
    public function seoOgEdit(int $siteId, SiteRepository $siteRepository, UserSiteRepository $userSiteRepository, Security $security, Request $request): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$userSiteRepository->findBy(['user' => $security->getUser(), 'site' => $site])) {
            throw $this->createNotFoundException('Nie masz dostępu do tej strony.');
        }
        $form = $this->createForm(MetaOgType::class, $site);
        $imageBeforeSave = $site->getOgImage();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('ogImage')->getData();
            if ($image) {
                $newFileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('post_images_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    throw $this->createNotFoundException('Błąd podczas dodawania pliku');
                }
                $site->setOgImage($newFileName);
            } else {
                if ($imageBeforeSave != null) {
                    $site->setOgImage($imageBeforeSave);
                }
            }
            $siteRepository->save($site, true);
            $this->addFlash('success', 'Pomyślnie zaktualizowano metadane.');

            return $this->redirectToRoute('app_site_seo_show', ['siteId' => $siteId]);
        }

        return $this->render('defaults/edit.html.twig', [
            'title' => 'Edycja metadanych',
            'form' => $form
        ]);
    }

    #[Route('/{siteId}/seo-twitter/edit', name: 'app_site_seo_twitter_edit')]
    public function seoTwitterEdit(int $siteId, SiteRepository $siteRepository, UserSiteRepository $userSiteRepository, Security $security, Request $request): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$userSiteRepository->findBy(['user' => $security->getUser(), 'site' => $site])) {
            throw $this->createNotFoundException('Nie masz dostępu do tej strony.');
        }
        $form = $this->createForm(MetaTwitterType::class, $site);
        $imageBeforeSave = $site->getOgImage();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('twitterImage')->getData();
            if ($image) {
                $newFileName = uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('post_images_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    throw $this->createNotFoundException('Błąd podczas dodawania pliku');
                }
                $site->setTwitterImage($newFileName);
            } else {
                if ($imageBeforeSave != null) {
                    $site->setTwitterImage($imageBeforeSave);
                }
            }
            $siteRepository->save($site, true);
            $this->addFlash('success', 'Pomyślnie zaktualizowano metadane.');

            return $this->redirectToRoute('app_site_seo_show', ['siteId' => $siteId]);
        }

        return $this->render('defaults/edit.html.twig', [
            'title' => 'Edycja metadanych',
            'form' => $form
        ]);
    }

    #[Route('/{siteId}/post-template', name: 'app_site_post_template')]
    public function postTemplate(int $siteId, SiteRepository $siteRepository, PostTemplateRepository $postTemplateRepository, Request $request): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$postTemplate = $postTemplateRepository->findOneBy(['site' => $site])) {
            $postTemplate = new PostTemplate();
        }
        $postTemplate->setSite($site);
        $form = $this->createForm(PostTemplateType::class, $postTemplate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $postTemplateRepository->save($postTemplate, true);
            $this->addFlash('success', 'Pomyślnie zaktualizowano szablon.');

            return $this->redirectToRoute('app_site_post_template', ['siteId' => $siteId]);
        }

        return $this->render('defaults/edit.html.twig', [
            'title' => 'Edycja szablonu postów',
            'form' => $form,
            'back_link' => 'app_site_show'
        ]);
    }
}
