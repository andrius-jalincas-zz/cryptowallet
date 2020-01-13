<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\User;
use App\Service\Asset as AssetService;
use App\Service\Currency;
use Symfony\Component\HttpFoundation\Request;
use  Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
     */
    public function list()
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
     */
    public function add(Request $request, AssetService $assetService)
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
     */
    public function update(Request $request, int $id, AssetService $assetService)
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
     */
    public function remove(Request $request, int $id, AssetService $assetService)
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
     */
    public function balance(Request $request, Currency $currencyService)
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
