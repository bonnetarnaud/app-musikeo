<?php

namespace App\Entity;

use App\Repository\InstrumentRentalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstrumentRentalRepository::class)]
class InstrumentRental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Instrument::class, inversedBy: 'rentalHistory')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Instrument $instrument = null;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'instrumentRentals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $student = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    private ?string $monthlyPrice = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null; // 'active', 'returned', 'overdue'

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_OVERDUE = 'overdue';

    public function __construct()
    {
        $this->startDate = new \DateTime();
        $this->status = self::STATUS_ACTIVE;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstrument(): ?Instrument
    {
        return $this->instrument;
    }

    public function setInstrument(?Instrument $instrument): static
    {
        $this->instrument = $instrument;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getMonthlyPrice(): ?string
    {
        return $this->monthlyPrice;
    }

    public function setMonthlyPrice(?string $monthlyPrice): static
    {
        $this->monthlyPrice = $monthlyPrice;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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

    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Actif',
            self::STATUS_RETURNED => 'Retourné',
            self::STATUS_OVERDUE => 'En retard',
            default => 'Inconnu'
        };
    }

    public static function getStatusChoices(): array
    {
        return [
            'Actif' => self::STATUS_ACTIVE,
            'Retourné' => self::STATUS_RETURNED,
            'En retard' => self::STATUS_OVERDUE,
        ];
    }

    public function __toString(): string
    {
        return $this->instrument?->getName() . ' - ' . $this->student?->getFullName() ?? '';
    }
}
