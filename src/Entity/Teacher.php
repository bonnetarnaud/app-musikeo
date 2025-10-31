<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
class Teacher extends User
{
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $biography = null;

    /**
     * @var Collection<int, Instrument>
     */
    #[ORM\ManyToMany(targetEntity: Instrument::class, inversedBy: 'teachers')]
    private Collection $instrumentsTaught;

    /**
     * @var Collection<int, Course>
     */
    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'teacher')]
    private Collection $courses;

    public function __construct()
    {
        $this->instrumentsTaught = new ArrayCollection();
        $this->courses = new ArrayCollection();
        $this->setRoles(['ROLE_TEACHER']);
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

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): static
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * @return Collection<int, Instrument>
     */
    public function getInstrumentsTaught(): Collection
    {
        return $this->instrumentsTaught;
    }

    public function addInstrumentsTaught(Instrument $instrumentsTaught): static
    {
        if (!$this->instrumentsTaught->contains($instrumentsTaught)) {
            $this->instrumentsTaught->add($instrumentsTaught);
        }

        return $this;
    }

    public function removeInstrumentsTaught(Instrument $instrumentsTaught): static
    {
        $this->instrumentsTaught->removeElement($instrumentsTaught);

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setTeacher($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getTeacher() === $this) {
                $course->setTeacher(null);
            }
        }

        return $this;
    }
}