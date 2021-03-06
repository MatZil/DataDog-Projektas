<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(
     *     max = 180,
     *     maxMessage="Your email can't contain more than 180 characters"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9]+$/",
     *     message="Your username has to contain only alphanumeric values"
     * )
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 4,
     *     max = 20,
     *     minMessage = "Your username has to contain at least 4 characters",
     *     maxMessage = "Your username can't contain more than 20 characters"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]+$/",
     *     message="Your surname has to contain only alphabetic values"
     * )
     * @Assert\Length(
     *     max = 40,
     *     maxMessage="Your surname can't contain more than 40 characters"
     * )
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]+$/",
     *     message="Your first name has to contain only alphabetic values"
     * )
     * @Assert\Length(
     *     min=3,
     *     max = 20,
     *     minMessage="Your first name has to contain at least 3 characters",
     *     maxMessage="Your first name can't contain more than 20 characters"
     * )
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActivated = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user")
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="users")
     */
    private $subscribedCategories;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->subscribedCategories = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getSubscribedCategories(): Collection
    {
        return $this->subscribedCategories;
    }

    public function addSubscribedCategory(Category $subscribedCategory): self
    {
        if (!$this->subscribedCategories->contains($subscribedCategory)) {
            $this->subscribedCategories[] = $subscribedCategory;
        }

        return $this;
    }

    public function removeSubscribedCategory(Category $subscribedCategory): self
    {
        if ($this->subscribedCategories->contains($subscribedCategory)) {
            $this->subscribedCategories->removeElement($subscribedCategory);
        }

        return $this;
    }
}
