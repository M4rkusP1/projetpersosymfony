<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Mime\Message;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
class Personne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:'Le prénom ne peut pas être vide')]
    #[Assert\Length(
        min: 2,
        max: 30,
        minMessage: 'Votre prénom doit faire au moins 2 caractères',
        maxMessage: 'Votre prénom ne peut pas dépasser 30 caractères',
    )]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Votre prénom ne peut pas contenir de nombre',
    )]
    private ?string $Firstname = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message:'Le nom ne peut pas être vide')]
    #[Assert\Length(
        min: 2,
        max: 40,
        minMessage: 'Votre nom doit faire au moins 2 caractères',
        maxMessage: 'Votre nom ne peut pas dépasser 30 caractères',
    )]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Votre nom ne peut pas contenir de nombre',
    )]
    private ?string $Name = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'L\'âge ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/\[a-z]/',
        match: false,
        message: 'Votre âge ne peut pas contenir de lettres',
    )]
    private ?int $age = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToOne(inversedBy: 'personne', cascade: ['persist', 'remove'])]
    private ?Profile $profile = null;

    #[ORM\ManyToMany(targetEntity: Hobby::class)]
    private Collection $hobbies;

    #[ORM\ManyToOne(inversedBy: 'personnes')]
    private ?Profession $job = null;

    public function __construct()
    {
        $this->hobbies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->Firstname;
    }

    public function setFirstname(string $Firstname): self
    {
        $this->Firstname = $Firstname;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Collection<int, hobby>
     */
    public function getHobbies(): Collection
    {
        return $this->hobbies;
    }

    public function addHobby(hobby $hobby): self
    {
        if (!$this->hobbies->contains($hobby)) {
            $this->hobbies->add($hobby);
        }

        return $this;
    }

    public function removeHobby(Hobby $hobby): self
    {
        $this->hobbies->removeElement($hobby);

        return $this;
    }

    public function getJob(): ?Profession
    {
        return $this->job;
    }

    public function setJob(?Profession $job): self
    {
        $this->job = $job;

        return $this;
    }
}
