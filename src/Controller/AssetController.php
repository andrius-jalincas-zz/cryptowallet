<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/", name="asset", methods={"GET"})
     */
    public function index()
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
}
