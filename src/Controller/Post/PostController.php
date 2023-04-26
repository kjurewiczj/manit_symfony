<?php

namespace App\Controller\Post;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\PostTemplateRepository;
use App\Repository\SiteRepository;
use App\Repository\UserSiteRepository;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/{siteId}/create', name: 'app_post_create')]
    public function new(int $siteId, SiteRepository $siteRepository, PostRepository $postRepository, Request $request, UserSiteRepository $userSiteRepository, PostTemplateRepository $postTemplateRepository, Security $security): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$userSiteRepository->findBy(['user' => $security->getUser(), 'site' => $site])) {
            throw $this->createNotFoundException('Nie masz dostępu do tej strony.');
        }
        if (!$postTemplate = $postTemplateRepository->findOneBy(['site' => $site])) {
            $this->addFlash('danger', 'Aby utworzyć post musisz skonfiurować szablon.');

            return $this->redirectToRoute('app_site_post_template', ['siteId' => $siteId]);
        }
        if (!$postTemplate->isTitle() && !$postTemplate->isDescription() && !$postTemplate->isImage() && !$postTemplate->isPrice()) {
            $this->addFlash('danger', 'Aby utworzyć post zaznacz przynajmniej jedną opcję.');

            return $this->redirectToRoute('app_site_post_template', ['siteId' => $siteId]);
        }
        $post = new Post();
        $post->setSite($site);
        $form = $this->createForm(PostType::class, $post, ['postTemplate' => $postTemplate]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('image')) {
                if ($image = $form->get('image')->getData()) {
                    $newFileName = uniqid().'.'.$image->guessExtension();
                    try {
                        $image->move(
                            $this->getParameter('post_images_directory'),
                            $newFileName
                        );
                    } catch (FileException $e) {
                        throw $this->createNotFoundException('Błąd podczas dodawania pliku.');
                    }
                    $post->setImage($newFileName);
                }
            }
            $postRepository->save($post, true);
            $this->addFlash('success', 'Pomyślnie dodano post.');
            if (!$post->isStatus()) {
                $this->addFlash('secondary', 'Post nie będzie widoczny, ponieważ posiada status niekatywny.');
            }

            return $this->redirectToRoute('app_site_show', ['siteId' => $siteId]);
        }

        return $this->render('defaults/new.html.twig', [
            'title' => 'Tworzenie posta',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{siteId}/show/{postId}', name: 'app_post_show')]
    public function show(int $siteId, int $postId, SiteRepository $siteRepository, PostRepository $postRepository, UserSiteRepository $userSiteRepository, Security $security): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$userSiteRepository->findBy(['user' => $security->getUser(), 'site' => $site])) {
            throw $this->createNotFoundException('Nie masz dostępu do tej strony.');
        }
        if (!$post = $postRepository->find($postId)) {
            throw $this->createNotFoundException('W systemie nie istnieje post o takim id.');
        }

        return $this->render('post/show.html.twig', [
            'title' => 'Podgląd ' . $post->getTitle(),
            'post' => $post,
            'back_link' => 'app_site_show'
        ]);
    }

    #[Route('/{siteId}/edit/{postId}', name: 'app_post_edit')]
    public function edit(int $siteId, int $postId, SiteRepository $siteRepository, PostRepository $postRepository, Request $request, UserSiteRepository $userSiteRepository, Security $security, PostTemplateRepository $postTemplateRepository): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$userSiteRepository->findBy(['user' => $security->getUser(), 'site' => $site])) {
            throw $this->createNotFoundException('Nie masz dostępu do tej strony.');
        }
        if (!$post = $postRepository->find($postId)) {
            throw $this->createNotFoundException('W systemie nie istnieje post o takim id.');
        }
        if (!$postTemplate = $postTemplateRepository->findOneBy(['site' => $site])) {
            $this->addFlash('danger', 'Aby utworzyć post musisz skonfiurować szablon.');

            return $this->redirectToRoute('app_site_post_template', ['siteId' => $siteId]);
        }
        if (!$postTemplate->isTitle() && !$postTemplate->isDescription() && !$postTemplate->isImage() && !$postTemplate->isPrice()) {
            $this->addFlash('danger', 'Aby utworzyć post zaznacz przynajmniej jedną opcję.');

            return $this->redirectToRoute('app_site_post_template', ['siteId' => $siteId]);
        }
        $form = $this->createForm(PostType::class, $post, ['postTemplate' => $postTemplate]);
        $imageBeforeSave = $post->getImage();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('image')) {
                if ($image = $form->get('image')->getData()) {
                    $newFileName = uniqid() . '.' . $image->guessExtension();
                    try {
                        $image->move(
                            $this->getParameter('post_images_directory'),
                            $newFileName
                        );
                    } catch (FileException $e) {
                        throw $this->createNotFoundException('Błąd podczas dodawania pliku');
                    }
                    $post->setImage($newFileName);
                } else {
                    if ($imageBeforeSave != null) {
                        $post->setImage($imageBeforeSave);
                    }
                }
            }
            $postRepository->save($post, true);
            $this->addFlash('success', 'Pomyślnie zaktualizowano post.');

            return $this->redirectToRoute('app_site_show', ['siteId' => $siteId]);
        }

        return $this->render('defaults/edit.html.twig', [
            'title' => 'Edycja posta',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{siteId}/delete/{postId}', name: 'app_post_delete')]
    public function delete(int $siteId, int $postId, PostRepository $postRepository, SiteRepository $siteRepository, UserSiteRepository $userSiteRepository, Security $security): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$userSiteRepository->findBy(['user' => $security->getUser(), 'site' => $site])) {
            throw $this->createNotFoundException('Nie masz dostępu do tej strony.');
        }
        if (!$post = $postRepository->find($postId)) {
            throw $this->createNotFoundException('W systemie nie istnieje post o takim id.');
        }
        $postRepository->remove($post, true);
        $this->addFlash('success', 'Pomyślnie usunięto post.');

        return $this->redirectToRoute('app_site_show', ['siteId' => $siteId]);
    }

    #[Route('/{siteId}/generatePdf/{postId}', name: 'app_post_generate_pdf')]
    public function generatePdf(int $siteId, int $postId, SiteRepository $siteRepository, PostRepository $postRepository, Pdf $pdf, UserSiteRepository $userSiteRepository, Security $security): Response
    {
        if (!$site = $siteRepository->find($siteId)) {
            throw $this->createNotFoundException('W systemie nie istnieje strona o takim id.');
        }
        if (!$userSiteRepository->findBy(['user' => $security->getUser(), 'site' => $site])) {
            throw $this->createNotFoundException('Nie masz dostępu do tej strony.');
        }
        if (!$post = $postRepository->find($postId)) {
            throw $this->createNotFoundException('W systemie nie istnieje post o takim id.');
        }
        $content = '<meta charset="UTF-8"/>';
        $content .= '<style>@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap");* {font-family: "Poppins", sans-serif;}</style>';
        $content .= $post->getContent();

        return new Response(
            $pdf->getOutputFromHtml($content),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $post->getTitle() . '.pdf'),
            ]
        );
    }
}
