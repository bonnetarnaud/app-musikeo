<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDatetime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDatetime = null;

    #[ORM\ManyToOne(inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getStartDatetime(): ?\DateTimeInterface
    {
        return $this->startDatetime;
    }

    public function setStartDatetime(\DateTimeInterface $startDatetime): static
    {
        $this->startDatetime = $startDatetime;

        return $this;
    }

    public function getEndDatetime(): ?\DateTimeInterface
    {
        return $this->endDatetime;
    }

    public function setEndDatetime(\DateTimeInterface $endDatetime): static
    {
        $this->endDatetime = $endDatetime;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getDuration(): ?\DateInterval
    {
        if ($this->startDatetime && $this->endDatetime) {
            return $this->startDatetime->diff($this->endDatetime);
        }
        
        return null;
    }

    public function __toString(): string
    {
        return $this->course?->getName() . ' - ' . $this->startDatetime?->format('Y-m-d H:i') ?? '';
    }
}