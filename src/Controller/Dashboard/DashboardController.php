<?php

namespace App\Controller\Dashboard;

use App\Handler\StatisticsHandler;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard_index')]
    public function index(StatisticsHandler $statisticsHandler, SiteRepository $siteRepository, Security $security): Response
    {
        $statistics = $statisticsHandler->getStatisticsForDashboard();

        return $this->render('dashboard/index.html.twig', [
            'title' => 'Panel główny',
            'statistics' => $statistics,
        ]);
    }
}
