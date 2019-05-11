<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SecurityCodeRepository")
 */
class SecurityCode
{
    public const EMAIL_CONFIRMATION = 1;
    public const PASSWORD_RESET = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $purpose;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function setSecureCode(int $length = 10): self
    {
        $random = sha1(random_bytes($length));
        return $this->setCode($random);
    }

    public function getPurpose(): ?int
    {
        return $this->purpose;
    }

    public function setPurpose(?int $purpose): self
    {
        $this->purpose = $purpose;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }
}
