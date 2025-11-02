<?php

namespace App\Entity;

use App\Repository\PreRegistrationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PreRegistrationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PreRegistration
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_ENROLLED = 'enrolled';
    public const STATUS_REJECTED = 'rejected';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    #[Assert\Length(max: 100)]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(max: 100)]
    private ?string $lastname = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'Veuillez saisir un email valide')]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    private ?string $parentName = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email(message: 'Veuillez saisir un email valide')]
    private ?string $parentEmail = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $parentPhone = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Veuillez choisir un instrument')]
    private ?string $interestedInstrument = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Veuillez indiquer votre niveau')]
    private ?string $level = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $contactedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(inversedBy: 'preRegistrations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getParentName(): ?string
    {
        return $this->parentName;
    }

    public function setParentName(?string $parentName): static
    {
        $this->parentName = $parentName;
        return $this;
    }

    public function getParentEmail(): ?string
    {
        return $this->parentEmail;
    }

    public function setParentEmail(?string $parentEmail): static
    {
        $this->parentEmail = $parentEmail;
        return $this;
    }

    public function getParentPhone(): ?string
    {
        return $this->parentPhone;
    }

    public function setParentPhone(?string $parentPhone): static
    {
        $this->parentPhone = $parentPhone;
        return $this;
    }

    public function getInterestedInstrument(): ?string
    {
        return $this->interestedInstrument;
    }

    public function setInterestedInstrument(string $interestedInstrument): static
    {
        $this->interestedInstrument = $interestedInstrument;
        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getContactedAt(): ?\DateTimeInterface
    {
        return $this->contactedAt;
    }

    public function setContactedAt(?\DateTimeInterface $contactedAt): static
    {
        $this->contactedAt = $contactedAt;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;
        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTime();
    }

    public function getFullName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getAge(): ?int
    {
        if (!$this->dateOfBirth) {
            return null;
        }

        return $this->dateOfBirth->diff(new \DateTime())->y;
    }

    public function isMinor(): bool
    {
        $age = $this->getAge();
        return $age !== null && $age < 18;
    }

    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_CONTACTED => 'Contacté',
            self::STATUS_ENROLLED => 'Inscrit',
            self::STATUS_REJECTED => 'Refusé',
        ];
    }

    public static function getAvailableInstruments(): array
    {
        return [
            'piano' => 'Piano',
            'guitar' => 'Guitare',
            'violin' => 'Violon',
            'drums' => 'Batterie',
            'flute' => 'Flûte',
            'clarinet' => 'Clarinette',
            'trumpet' => 'Trompette',
            'saxophone' => 'Saxophone',
            'voice' => 'Chant',
            'bass' => 'Basse',
            'cello' => 'Violoncelle',
            'other' => 'Autre',
        ];
    }

    public static function getAvailableLevels(): array
    {
        return [
            'beginner' => 'Débutant',
            'intermediate' => 'Intermédiaire',
            'advanced' => 'Avancé',
        ];
    }
}