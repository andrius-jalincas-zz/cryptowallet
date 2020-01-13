<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ApiToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use OpenApi\Annotations as OA;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/api/login", name="api.login", methods={"POST"})
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @param ApiToken $apiTokenService
     * @return JsonResponse
     * @throws \Exception
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"username": "test", "password": "test"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *     )
     * )
     */
    public function apiLogin(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        ApiToken $apiTokenService
    ): JsonResponse
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
