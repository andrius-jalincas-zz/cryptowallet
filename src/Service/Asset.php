<?php


namespace App\Service;


use App\Entity\Currency;
use App\Entity\User;
use App\Entity\Asset as Entity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Asset
{
    /** @var ValidatorInterface */
    private $validator;
    /** @var EntityManagerInterface */
    private $em;

    /**
     * Asset constructor.
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $this->validator = $validator;
        $this->em = $em;
    }

    /**
     * @param array $assetData
     * @param User $user
     * @param null $assetId
     */
    public function handleAssetData(array $assetData, User $user, $assetId = null): void
    {
        $asset = new Entity();
        if ($assetId) {
            $asset = $this->getAssetByIdAndUserId($assetId, $user->getId());
        }
        $asset = $this->processAssetData($asset, $assetData, $user);
        $this->validate($asset);
        $this->save($asset);
    }

    /**
     * @param Entity $asset
     */
    private function save(Entity $asset): void
    {
        $this->em->persist($asset);
        $this->em->flush();
    }

    /**
     * @param Entity $asset
     */
    private function validate(Entity $asset): void
    {
        $errors = $this->validator->validate($asset);
        if (count($errors) > 0) {
            $errorString = (string) $errors;
            throw new \InvalidArgumentException($errorString, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param Entity $asset
     * @param array $assetData
     * @param User $user
     * @return Entity
     */
    public function processAssetData(Entity $asset, array $assetData, User $user): Entity
    {
        if (!empty($assetData['label'])) {
            $asset->setLabel($assetData['label']);
        }
        if (!empty($assetData['amount'])) {
            $asset->setAmount($assetData['amount']);
        }
        if (!empty($assetData['currency'])) {
            $currencyRepository = $this->em->getRepository(Currency::class);
            $currency = $currencyRepository->findOneBy(['slug' => $assetData['currency']]);
            if (null === $currency) {
                throw new \InvalidArgumentException(
                    sprintf("%s currency is not supported", $assetData['currency']),
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            $asset->setCurrency($currency);
        }
        $asset->setUser($user);

        return $asset;
    }

    /**
     * @param int $id
     * @param int $userId
     * @return Entity|null
     */
    private function getAssetByIdAndUserId(int $id, int $userId): ?Entity
    {
        $repository = $this->em->getRepository(Entity::class);
        $asset = $repository->findOneBy([
            'id' => $id,
            'user_id' => $userId
        ]);

        if (null === $asset) {
            throw new \InvalidArgumentException("You have no such asset", Response::HTTP_NOT_FOUND);
        }

        return $asset;
    }

    /**
     * @param int $id
     * @param int $userId
     */
    public function deleteByIdAndUserId(int $id, int $userId): void
    {
        $asset = $this->getAssetByIdAndUserId($id, $userId);
        $this->em->remove($asset);
        $this->em->flush();
    }
}