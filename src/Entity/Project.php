<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"project_id", "show_project"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_project", "delete_project"})
     */
    private $Name;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"show_project", "delete_project"})
     */
    private $Deadline;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"show_project", "delete_project"})
     */
    private $ProgrammerCount;

    /**
     * @ORM\ManyToMany(targetEntity=Programmer::class, mappedBy="Projects")
     * @Groups({"show_project"})
     */
    private $programmers;

    /**
     * @ORM\OneToMany(targetEntity=Bug::class, mappedBy="Project", orphanRemoval=true)
     * @Groups({"show_project"})
     */
    private $bugs;

    public function __construct()
    {
        $this->programmers = new ArrayCollection();
        $this->bugs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->Deadline;
    }

    public function setDeadline(\DateTimeInterface $Deadline): self
    {
        $this->Deadline = $Deadline;

        return $this;
    }

    public function getProgrammerCount(): ?int
    {
        return $this->ProgrammerCount;
    }

    public function setProgrammerCount(int $ProgrammerCount): self
    {
        $this->ProgrammerCount = $ProgrammerCount;

        return $this;
    }

    /**
     * @return Collection|Programmer[]
     */
    public function getProgrammers(): Collection
    {
        return $this->programmers;
    }

    public function addProgrammer(Programmer $programmer): self
    {
        if (!$this->programmers->contains($programmer)) {
            $this->programmers[] = $programmer;
            $programmer->addProject($this);
        }

        return $this;
    }

    public function removeProgrammer(Programmer $programmer): self
    {
        if ($this->programmers->removeElement($programmer)) {
            $programmer->removeProject($this);
        }

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
            $bug->setProject($this);
        }

        return $this;
    }

    public function removeBug(Bug $bug): self
    {
        if ($this->bugs->removeElement($bug)) {
            // set the owning side to null (unless already changed)
            if ($bug->getProject() === $this) {
                $bug->setProject(null);
            }
        }

        return $this;
    }
}
