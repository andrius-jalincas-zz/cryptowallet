<?php


namespace App\Service;


use App\Entity\User;
use App\Entity\ApiToken as Entity;
use Doctrine\ORM\EntityManagerInterface;

class ApiToken
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ApiToken constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param User $user
     * @return Entity
     * @throws \Exception
     */
    public function handleUserToken(User $user): Entity
    {
        /** @var Entity $apiToken */
        $apiToken = $user->getApiToken();
        $currentDate = new \DateTime();
        if (!$apiToken) {
            $apiToken = $this->generateToken($user);
        } else if ($apiToken->getExpiry() < $currentDate) {
            $apiToken = $this->refreshToken($apiToken, $currentDate);
        }

        return $apiToken;
    }

    /**
     * @param Entity $apiToken
     * @param \DateTime $currentDate
     * @return Entity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function refreshToken(Entity $apiToken, \DateTime $currentDate): Entity
    {
        $newToken = $this->generateTokenHash($currentDate);
        $apiToken->setToken($newToken);
        $apiToken->setExpiry($currentDate->modify('+ 2 week'));
        $this->em->persist($apiToken);
        $this->em->flush($apiToken);

        return $apiToken;
    }

    /**
     * @param User $user
     * @return Entity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function generateToken(User $user): Entity
    {
        $token = new Entity();
        $date = new \DateTime();
        $tokenHash = $this->generateTokenHash($date);
        $token->setToken($tokenHash);
        $token->setExpiry($date->modify('+ 2 week'));
        $token->setUserId($user->getId());
        $token->setUser($user);
        $this->em->persist($token);
        $this->em->flush();

        return $token;
    }

    /**
     * @param \DateTime $dateTime
     * @return string
     */
    private function generateTokenHash(\DateTime $dateTime): string
    {
        $secret = $dateTime->format("Y-m-d H:i:s") . "can_i_join_tesonet_pls" . rand(0,9999);

        return hash("sha256", $secret);
    }
}