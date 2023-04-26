<?php
namespace App\Handler;

use App\Entity\Site;
use App\Repository\PostRepository;
use App\Repository\PostTemplateRepository;
use App\Repository\UserSiteRepository;
use Symfony\Bundle\SecurityBundle\Security;

class StatisticsHandler
{
    public function __construct(
        private UserSiteRepository $userSiteRepository,
        private PostRepository $postRepository,
        private PostTemplateRepository $postTemplateRepository,
        private Security $security,
    )
    {
    }

    public function getStatisticsForDashboard(): array
    {
        $statistics['sitesCount'] = $this->getCountUserSite();
        $statistics['postsCount'] = $this->getCountPost();
        $statistics['warnings'] = $this->generateWarnings();

        return $statistics;
    }

    public function getCountUserSite(): int
    {
        return count($this->userSiteRepository->findBy(['user' => $this->security->getUser()]));
    }

    public function getCountPost(): int
    {
        return count($this->postRepository->findBy(['userCreated' => $this->security->getUser()]));
    }

    public function generateWarnings(): array
    {
        $userSites = $this->userSiteRepository->findBy(['user' => $this->security->getUser()]);
        $sites = $warnings = [];
        foreach ($userSites as $userSite) {
            $sites[] = $userSite->getSite();
        }
        foreach ($sites as $site) {
            if ($this->isSeoNotCompleted($site)) {
                if (!isset($warnings[$site->getName()])) {
                    $warnings[$site->getName()][] = ' <a href="/sites/' . $site->getId() . '/seo">posiada braki w SEO</a>';
                }
                if (!$this->postTemplateRepository->findOneBy(['site' => $site])) {
                    $warnings[$site->getName()][] = ' <a href="/sites/' . $site->getId() . '/post-template">posiada nieuzupełniony szablon postów</a>';
                }
            }
        }

        return $warnings;
    }

    public function isSeoNotCompleted(Site $site): bool
    {
        if (!$site->getMetaTitle() || !$site->getMetaDescription() || !$site->getOgImage() || !$site->getOgTitle() || !$site->getOgDescription() || !$site->getTwitterImage() || !$site->getTwitterTitle() || !$site->getTwitterDescription()) {
            return true;
        }

        return false;
    }
}