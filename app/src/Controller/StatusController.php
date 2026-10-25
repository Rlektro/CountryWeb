<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


final class StatusController extends AbstractController
{
    #[Route('/api', name: 'app_status', methods: ['GET'])]
    public function api(Request $request): JsonResponse
    {
        return $this->json([
            'status' => 'server is running',
            'host' => $request->getHost(),
            'protocol' => $request->getScheme() // не было скобок :(
        ]);
    }
    #[Route('/api/ping', name: 'ping_status', methods: ['GET'])]
    public function ping(): JsonResponse
    {
        return $this->json([
            'status' => 'pong'
        ]);
    }
}
