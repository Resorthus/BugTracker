<?php

namespace App\Entity;

use App\Repository\ProgrammerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProgrammerRepository::class)
 */
class Programmer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"show_programmer", "programmer_id"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_programmer", "delete_programmer"})
     */
    private $FirstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_programmer", "delete_programmer"})
     */
    private $LastName;

    /**
     * @ORM\Column(type="date")
     * @Groups({"show_programmer", "delete_programmer"})
     */
    private $Birthdate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_programmer", "delete_programmer"})
     */
    private $Level;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_programmer", "delete_programmer"})
     */
    private $Specialization;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_programmer", "delete_programmer"})
     */
    private $Technology;

    /**
     * @ORM\OneToMany(targetEntity=Bug::class, mappedBy="Responsibility")
     * @Groups({"show_programmer"})
     */
    private $bugs;

    /**
     * @ORM\OneToMany(targetEntity=Bug::class, mappedBy="SubmittedBy")
     * @Groups({"show_programmer"})
     */
    private $submittedBugs;

    public function __construct()
    {
        $this->bugs = new ArrayCollection();
        $this->submittedBugs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(string $FirstName): self
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(string $LastName): self
    {
        $this->LastName = $LastName;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->Birthdate;
    }

    public function setBirthdate(\DateTimeInterface $Birthdate): self
    {
        $this->Birthdate = $Birthdate;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->Level;
    }

    public function setLevel(string $Level): self
    {
        $this->Level = $Level;

        return $this;
    }

    public function getSpecialization(): ?string
    {
        return $this->Specialization;
    }

    public function setSpecialization(string $Specialization): self
    {
        $this->Specialization = $Specialization;

        return $this;
    }

    public function getTechnology(): ?string
    {
        return $this->Technology;
    }

    public function setTechnology(string $Technology): self
    {
        $this->Technology = $Technology;

        return $this;
    }

    /**
     * @return Collection|Bug[]
     */
    public function getBugs(): Collection
    {
        return $this->bugs;
    }

    public function addBug(Bug $bug): self
    {
        if (!$this->bugs->contains($bug)) {
            $this->bugs[] = $bug;
            $bug->setResponsibility($this);
        }

        return $this;
    }

    public function removeBug(Bug $bug): self
    {
        if ($this->bugs->removeElement($bug)) {
            // set the owning side to null (unless already changed)
            if ($bug->getResponsibility() === $this) {
                $bug->setResponsibility(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Bug[]
     */
    public function getSubmittedBugs(): Collection
    {
        return $this->submittedBugs;
    }

    public function addSubmittedBug(Bug $submittedBug): self
    {
        if (!$this->submittedBugs->contains($submittedBug)) {
            $this->submittedBugs[] = $submittedBug;
            $submittedBug->setSubmittedBy($this);
        }

        return $this;
    }

    public function removeSubmittedBug(Bug $submittedBug): self
    {
        if ($this->submittedBugs->removeElement($submittedBug)) {
            // set the owning side to null (unless already changed)
            if ($submittedBug->getSubmittedBy() === $this) {
                $submittedBug->setSubmittedBy(null);
            }
        }

        return $this;
    }
}
