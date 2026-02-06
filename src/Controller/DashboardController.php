<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

 
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        // Redirect to dashboard
        return $this->redirectToRoute('dashboard_crm');
    }

    #[Route('/dashboard', name: 'dashboard_crm')]
    public function index(): Response
    {
        // Mock data - replace with real data from your repositories
        $stats = [
            'invoices_awaiting' => ['current' => 45, 'total' => 76, 'amount' => 5569, 'percentage' => 56],
            'converted_leads' => ['current' => 48, 'total' => 86, 'completed' => 52, 'percentage' => 63],
            'projects_progress' => ['current' => 16, 'total' => 20, 'percentage' => 78],
            'conversion_rate' => ['rate' => 46.59, 'amount' => 2254, 'percentage' => 46],
        ];

        $notifications = $this->getMockNotifications();

        return $this->render('dashboard.html.twig', [
            'stats' => $stats,
            'notifications' => $notifications,
        ]);
    }

    #[Route('/dashboard/analytics', name: 'dashboard_analytics')]
    public function analytics(): Response
    {
        return $this->render('dashboard/analytics.html.twig', [
            'pageTitle' => 'Analytics Dashboard',
        ]);
    }

    /**
     * Mock notifications - replace with real repository call
     */
    private function getMockNotifications(): array
    {
        return [
            [
                'id' => 1,
                'userName' => 'Malanie Hanvey',
                'userAvatar' => '2.png',
                'message' => 'We should talk about that at lunch!',
                'createdAt' => new \DateTime('-2 minutes'),
            ],
            [
                'id' => 2,
                'userName' => 'Valentine Maton',
                'userAvatar' => '3.png',
                'message' => 'You can download the latest invoices now.',
                'createdAt' => new \DateTime('-36 minutes'),
            ],
            [
                'id' => 3,
                'userName' => 'Archie Cantones',
                'userAvatar' => '4.png',
                'message' => 'Don\'t forget to pickup Jeremy after school!',
                'createdAt' => new \DateTime('-53 minutes'),
            ],
        ];
    }
}
