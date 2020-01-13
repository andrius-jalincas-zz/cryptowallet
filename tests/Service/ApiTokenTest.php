<?php


namespace App\Tests\Service;


use App\Entity\User;
use App\Service\ApiToken;
use App\Entity\ApiToken as ApiTokenEntity;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ApiTokenTest extends TestCase
{
    private $service;

    public function setUp(): void
    {
        $emMock = $this->createMock(EntityManagerInterface::class);
        $this->service = new ApiToken($emMock);
    }

    /**
     * @test
     */
    public function itShouldGenerateToken()
    {
        $user = new User();
        $user->setId(1);
        $token = $this->service->generateToken($user);
        $this->assertInstanceOf(ApiTokenEntity::class, $token);
    }

    /**
     * @test
     */
    public function itShouldRefreshApiToken()
    {
        $now = new \DateTime();
        $user = new User();
        $token = new ApiTokenEntity();
        $token->setToken('asd');
        $token->setExpiry($now);
        $refreshedToken = $this->service->refreshToken($token, $now);

        $this->assertNotEquals('asd', $refreshedToken);
    }
}