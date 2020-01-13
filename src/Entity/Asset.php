<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetRepository")
 */
class Asset
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $label;

    /**
     * @ORM\Column(type="float")
     * @Assert\Positive()
     */
    private $amount;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="assets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $currency_id;

    /**
     * @ORM\OneToOne(targetEntity="Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $currency;

    /**
     * @ORM\Column(type="datetime")
     */
    private $last_updated;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     * @return $this
     */
    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCurrencyId(): ?int
    {
        return $this->currency_id;
    }

    /**
     * @param int $currency_id
     * @return $this
     */
    public function setCurrencyId(int $currency_id): self
    {
        $this->currency_id = $currency_id;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->last_updated;
    }

    /**
     * @param \DateTimeInterface $last_updated
     * @return $this
     */
    public function setLastUpdated(\DateTimeInterface $last_updated): self
    {
        $this->last_updated = $last_updated;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     *
     * @return $this
     */
    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }



}
