<?php

namespace App\Entity;

use App\Repository\InstrumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstrumentRepository::class)]
class Instrument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $serialNumber = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $model = null;

    #[ORM\Column]
    private ?bool $isRentable = null;

    #[ORM\Column]
    private ?bool $isCurrentlyRented = null;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Student $currentRenter = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $rentalStartDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $additionalInfo = null;

    #[ORM\Column(name: '`condition`', length: 50)]
    private ?string $condition = null; // 'excellent', 'good', 'fair', 'poor', 'needs_repair'

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;

    /**
     * @var Collection<int, InstrumentRental>
     */
    #[ORM\OneToMany(targetEntity: InstrumentRental::class, mappedBy: 'instrument')]
    private Collection $rentalHistory;

    public function __construct()
    {
        $this->rentalHistory = new ArrayCollection();
        $this->isRentable = false;
        $this->isCurrentlyRented = false;
        $this->condition = 'good';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(?string $serialNumber): static
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function isRentable(): ?bool
    {
        return $this->isRentable;
    }

    public function setIsRentable(bool $isRentable): static
    {
        $this->isRentable = $isRentable;

        return $this;
    }

    public function isCurrentlyRented(): ?bool
    {
        return $this->isCurrentlyRented;
    }

    public function setIsCurrentlyRented(bool $isCurrentlyRented): static
    {
        $this->isCurrentlyRented = $isCurrentlyRented;

        return $this;
    }

    public function getCurrentRenter(): ?Student
    {
        return $this->currentRenter;
    }

    public function setCurrentRenter(?Student $currentRenter): static
    {
        $this->currentRenter = $currentRenter;

        return $this;
    }

    public function getRentalStartDate(): ?\DateTimeInterface
    {
        return $this->rentalStartDate;
    }

    public function setRentalStartDate(?\DateTimeInterface $rentalStartDate): static
    {
        $this->rentalStartDate = $rentalStartDate;

        return $this;
    }

    public function getAdditionalInfo(): ?string
    {
        return $this->additionalInfo;
    }

    public function setAdditionalInfo(?string $additionalInfo): static
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function setCondition(string $condition): static
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @return Collection<int, InstrumentRental>
     */
    public function getRentalHistory(): Collection
    {
        return $this->rentalHistory;
    }

    public function addRentalHistory(InstrumentRental $rentalHistory): static
    {
        if (!$this->rentalHistory->contains($rentalHistory)) {
            $this->rentalHistory->add($rentalHistory);
            $rentalHistory->setInstrument($this);
        }

        return $this;
    }

    public function removeRentalHistory(InstrumentRental $rentalHistory): static
    {
        if ($this->rentalHistory->removeElement($rentalHistory)) {
            // set the owning side to null (unless already changed)
            if ($rentalHistory->getInstrument() === $this) {
                $rentalHistory->setInstrument(null);
            }
        }

        return $this;
    }

    public function getConditionLabel(): string
    {
        return match($this->condition) {
            'excellent' => 'Excellent',
            'good' => 'Bon',
            'fair' => 'Correct',
            'poor' => 'Mauvais',
            'needs_repair' => 'À réparer',
            default => 'Inconnu'
        };
    }

    public static function getConditionChoices(): array
    {
        return [
            'Excellent' => 'excellent',
            'Bon' => 'good',
            'Correct' => 'fair',
            'Mauvais' => 'poor',
            'À réparer' => 'needs_repair',
        ];
    }

    public function __toString(): string
    {
        $name = $this->name ?? '';
        if ($this->brand && $this->model) {
            return $name . ' - ' . $this->brand . ' ' . $this->model;
        }
        return $name;
    }
}