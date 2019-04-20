<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank()
     * @Assert\Length(
     * min = 6,
     * max = 30,
     * minMessage = "Minimum length is 6 characters.",
     * maxMessage = "Maximum length is 30 characters."
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     * min = 15,
     * max = 255,
     * minMessage = "Minimum length is 15 characters.",
     * maxMessage = "Maximum length is 255 characters."
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^\d+(,\d{1,2})?$/",
     *     message="This isn't a valid price."
     * )
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank()
     * @Assert\Length(
     * min = 10,
     * max = 30,
     * minMessage = "Minimum length is 10 characters.",
     * maxMessage = "Maximum length is 30 characters."
     * )
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank()
     * @Assert\Length(
     * min = 10,
     * max = 30,
     * minMessage = "Minimum length is 10 characters.",
     * maxMessage = "Maximum length is 30 characters."
     * )
     */
    private $intro;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     * @Assert\Image(
     *     minWidth = 200,
     *     minHeight = 200,
     *     maxWidth = 900,
     *     maxHeight = 900
     * )
     */
    private $photo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fk_Category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

   

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getFkCategory(): ?Category
    {
        return $this->fk_Category;
    }

    public function setFkCategory(?Category $fk_Category): self
    {
        $this->fk_Category = $fk_Category;

        return $this;
    }
}
