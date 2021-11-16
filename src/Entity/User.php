<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"show_programmer", "programmer_id", "show_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"show_programmer", "show_user"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"show_programmer", "show_user"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_programmer", "show_user"})
     */
    private $FirstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_programmer", "show_user"})
     */
    private $LastName;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show_programmer"})
     */
    private $Level;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show_programmer"})
     */
    private $Specialization;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show_programmer"})
     */
    private $Technology;

    /**
     * @ORM\ManyToMany(targetEntity=Project::class, inversedBy="users")
     * @Groups({"show_programmer"})
     */
    private $Projects;

    /**
     * @ORM\OneToMany(targetEntity=Bug::class, mappedBy="responsibleUser")
     * @Groups({"show_programmer"})
     */
    private $Bugs;

    /**
     * @ORM\OneToMany(targetEntity=Bug::class, mappedBy="submitter", orphanRemoval=true)
     * @Groups({"show_programmer"})
     */
    private $SubmittedBugs;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"show_programmer", "show_user"})
     */
    private $isConfirmed;

    /**
     * @ORM\Column(type="date")
     * @Groups({"show_programmer", "show_user"})
     */
    private $Birthdate;

    public function __construct()
    {
        $this->Projects = new ArrayCollection();
        $this->SubmittedBugs = new ArrayCollection();
        $this->Bugs = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
    

    public function getLevel(): ?string
    {
        return $this->Level;
    }

    public function setLevel(?string $Level): self
    {
        $this->Level = $Level;

        return $this;
    }

    public function getSpecialization(): ?string
    {
        return $this->Specialization;
    }

    public function setSpecialization(?string $Specialization): self
    {
        $this->Specialization = $Specialization;

        return $this;
    }

    public function getTechnology(): ?string
    {
        return $this->Technology;
    }

    public function setTechnology(?string $Technology): self
    {
        $this->Technology = $Technology;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->Projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->Projects->contains($project)) {
            $this->Projects[] = $project;
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        $this->Projects->removeElement($project);

        return $this;
    }

    /**
     * @return Collection|Bug[]
     */
    public function getBugs(): Collection
    {
        return $this->Bugs;
    }

    public function addBug(Bug $bug): self
    {
        if (!$this->Bugs->contains($bug)) {
            $this->Bugs[] = $bug;
            $bug->setResponsibleUser($this);
        }

        return $this;
    }

    public function removeBug(Bug $bug): self
    {
        if ($this->Bugs->removeElement($bug)) {
            // set the owning side to null (unless already changed)
            if ($bug->getResponsibleUser() === $this) {
                $bug->setResponsibleUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Bug[]
     */
    public function getSubmittedBugs(): Collection
    {
        return $this->SubmittedBugs;
    }

    public function addSubmittedBug(Bug $bug): self
    {
        if (!$this->SubmittedBugs->contains($bug)) {
            $this->SubmittedBugs[] = $bug;
            $bug->setSubmitter($this);
        }

        return $this;
    }

    public function removeSubmittedBug(Bug $bug): self
    {
        if ($this->SubmittedBugs->removeElement($bug)) {
            // set the owning side to null (unless already changed)
            if ($bug->getSubmitter() === $this) {
                $bug->setSubmitter(null);
            }
        }

        return $this;
    }

    public function getIsConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): self
    {
        $this->isConfirmed = $isConfirmed;

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
}
