<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ApiToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/api/login", name="api.login", methods={"POST"})
     */
    public function apiLogin(Request $request, AuthenticationUtils $authenticationUtils, ApiToken $apiTokenService)
    {
        $errors = $authenticationUtils->getLastAuthenticationError();
        if ($errors) {

            return $this->json($errors);
        }
        /** @var User $user */
        $user = $this->getUser();
        $apiToken = $apiTokenService->handleUserToken($user);

        return $this->json([
            'token' => $apiToken->getToken()
        ]);
    }

}
