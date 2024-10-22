<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthenticator
{
    private string $staticToken;

    public function __construct(string $staticToken)
    {
        $this->staticToken = $staticToken;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $token = $request->headers->get('Authorization');

        $protectedRoutes = [
            '/api/organizers/{id}' => ['PUT', 'DELETE'],
        ];

        foreach ($protectedRoutes as $route => $methods) {
            if ($this->matchRoute($request->getPathInfo(), $route) && in_array($request->getMethod(), $methods)) {
                if ($token !== 'Bearer ' . $this->staticToken) {
                    $response = new JsonResponse(['message' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
                    $event->setResponse($response);
                }
                break;
            }
        }
    }

    private function matchRoute(string $path, string $route): bool
    {
        $routePattern = preg_replace('/\{[^\}]+\}/', '[^/]+', $route);
        return preg_match('#^' . $routePattern . '$#', $path);
    }
}