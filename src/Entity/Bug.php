<?php

namespace App\Entity;

use App\Repository\BugRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BugRepository::class)
 */
class Bug
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"show_bug", "bug_id"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_bug", "bug_delete"})
     */
    private $Description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_bug", "bug_delete"})
     */
    private $Severity;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_bug", "bug_delete"})
     */
    private $Status;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"show_bug", "bug_delete"})
     */
    private $Date;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="bugs")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"show_bug", "bug_delete"})
     */
    private $Project;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Bug")
     * @Groups({"show_bug", "bug_delete"})
     */
    private $responsibleUser;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Bugs")
     * @Groups({"show_bug", "bug_delete"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $submitter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getSeverity(): ?string
    {
        return $this->Severity;
    }

    public function setSeverity(string $Severity): self
    {
        $this->Severity = $Severity;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->Status;
    }

    public function setStatus(string $Status): self
    {
        $this->Status = $Status;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): self
    {
        $this->Date = $Date;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->Project;
    }

    public function setProject(?Project $Project): self
    {
        $this->Project = $Project;

        return $this;
    }

    public function getResponsibleUser(): ?User
    {
        return $this->responsibleUser;
    }

    public function setResponsibleUser(?User $responsibleUser): self
    {
        $this->responsibleUser = $responsibleUser;

        return $this;
    }

    public function getSubmitter(): ?User
    {
        return $this->submitter;
    }

    public function setSubmitter(?User $submitter): self
    {
        $this->submitter = $submitter;

        return $this;
    }
}
