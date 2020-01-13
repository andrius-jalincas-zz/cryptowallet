<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\User;
use App\Service\Asset as AssetService;
use App\Service\Currency;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use  Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * Class AssetController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 * @Route("/api/asset", name="asset_")
 */
class AssetController extends AbstractController
{
    /**
     * @Route("/", name="asset.list", methods={"GET"})
     * @return JsonResponse
     * @OA\Get(
     *     path="/api/asset",
     *     summary="get user assets",
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function list(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $assets = $user->getAssets();

        $response = [];
        /** @var Asset $asset */
        foreach ($assets as $asset) {
            $response[] = [
                'id' => $asset->getId(),
                'label' => $asset->getLabel(),
                'amount' => $asset->getAmount(),
                'currency' => $asset->getCurrency()->getSlug()
            ];
        }

        if (empty($response)) {
            return $this->json(['message' => 'You have no assets'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($response);
    }

    /**
     * @Route("", name="asset.add", methods={"POST"})
     * @param Request $request
     * @param AssetService $assetService
     *
     * @return JsonResponse
     * @OA\Post(
     *     path="/api/asset",
     *     summary="add new asset",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="label",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="float"
     *                 ),
     *                 @OA\Property(
     *                     property="currency",
     *                     type="string"
     *                 ),
     *                 example={"label": "BTC", "currency": "BTC", "amount": 0.1}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function add(Request $request, AssetService $assetService): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (empty($requestData)) {

            return $this->json([
                "error" => "Request data is invalid"
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var User $user */
        $user = $this->getUser();
        try {
            $assetService->handleAssetData($requestData, $user);
        } catch (\InvalidArgumentException $exception) {
            return $this->json([
                'error' => $exception->getMessage()
            ], $exception->getCode());
        }

        return $this->json(null);
    }

    /**
     * @Route("/{id}", name="asset.update", methods={"PUT"})
     * @param Request $request
     * @param int $id
     * @param AssetService $assetService
     *
     * @return JsonResponse
     *
     * @OA\Put(
     *     path="/api/asset/{id}",
     *     summary="update asset",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID asset",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int8"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="label",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="float"
     *                 ),
     *                 @OA\Property(
     *                     property="currency",
     *                     type="string"
     *                 ),
     *                 example={"label": "BTC", "currency": "BTC", "amount": 0.2}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function update(Request $request, int $id, AssetService $assetService): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (empty($requestData)) {

            return $this->json([
                "error" => "Request data is invalid"
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var User $user */
        $user = $this->getUser();
        try {
            $assetService->handleAssetData($requestData, $user, $id);
        } catch (\InvalidArgumentException $exception) {
            return $this->json([
                'error' => $exception->getMessage()
            ], $exception->getCode());
        }

        return $this->json(null);
    }

    /**
     * @Route("/{id}", name="asset.remove", methods={"DELETE"})
     * @param Request $request
     * @param int $id
     * @param AssetService $assetService
     *
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/asset/{id}",
     *     summary="delete asset",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID asset",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int8"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function remove(Request $request, int $id, AssetService $assetService): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        try {
            $assetService->deleteByIdAndUserId($id, $user->getId());
        } catch (\InvalidArgumentException $exception) {
            return $this->json([
                'error' => $exception->getMessage()
            ], $exception->getCode());
        }

        return $this->json(null);
    }
    /**
     * @Route("/balance", name="asset.balance", methods={"GET"})
     * @param Request $request
     * @param Currency $currencyService
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/asset/balance",
     *     summary="get user assets with value in usd",
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */
    public function balance(Request $request, Currency $currencyService): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $assets = $user->getAssets();

        $response = [];
        $totalValue = 0;
        /** @var Asset $asset */
        foreach ($assets as $asset) {
            $currency = $asset->getCurrency();
            $usdValue = $currencyService->getValueInUsd($currency, $asset->getAmount());
            $totalValue += $usdValue;
            $response[$asset->getId()] = [
                'label' => $asset->getLabel(),
                'amount' => $asset->getAmount(),
                'currency' => $currency->getSlug(),
                'USD_value' => $usdValue
            ];
        }
        $response['total_value'] = round($totalValue, 2, PHP_ROUND_HALF_UP);

        return $this->json($response);
    }
}
