<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/public')]
class PublicController extends AbstractController
{
    #[Route('/stats', name: 'api_public_stats', methods: ['GET'])]
    public function getStats(): JsonResponse
    {
        // Public statistics that anyone can access
        return $this->json([
            'total_users' => 100,
            'total_datasets' => 50,
            'last_updated' => new \DateTime()
        ]);
    }
} 