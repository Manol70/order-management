<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 55)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: GlassHistory::class)]
    private Collection $glassHistories;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: StatusHistory::class)]
    private Collection $statusHistories;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MosquitoHistory::class)]
    private Collection $mosquitoHistories;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DetailHistory::class)]
    private Collection $detailHistories;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist'])]
    private ?Customer $customer = null;

    

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->glassHistories = new ArrayCollection();
        $this->statusHistories = new ArrayCollection();
        $this->mosquitoHistories = new ArrayCollection();
        $this->detailHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
       // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GlassHistory>
     */
    public function getGlassHistories(): Collection
    {
        return $this->glassHistories;
    }

    public function addGlassHistory(GlassHistory $glassHistory): static
    {
        if (!$this->glassHistories->contains($glassHistory)) {
            $this->glassHistories->add($glassHistory);
            $glassHistory->setUser($this);
        }

        return $this;
    }

    public function removeGlassHistory(GlassHistory $glassHistory): static
    {
        if ($this->glassHistories->removeElement($glassHistory)) {
            // set the owning side to null (unless already changed)
            if ($glassHistory->getUser() === $this) {
                $glassHistory->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StatusHistory>
     */
    public function getStatusHistories(): Collection
    {
        return $this->statusHistories;
    }

    public function addStatusHistory(StatusHistory $statusHistory): static
    {
        if (!$this->statusHistories->contains($statusHistory)) {
            $this->statusHistories->add($statusHistory);
            $statusHistory->setUser($this);
        }

        return $this;
    }

    public function removeStatusHistory(StatusHistory $statusHistory): static
    {
        if ($this->statusHistories->removeElement($statusHistory)) {
            // set the owning side to null (unless already changed)
            if ($statusHistory->getUser() === $this) {
                $statusHistory->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MosquitoHistory>
     */
    public function getMosquitoHistories(): Collection
    {
        return $this->mosquitoHistories;
    }

    public function addMosquitoHistory(MosquitoHistory $mosquitoHistory): static
    {
        if (!$this->mosquitoHistories->contains($mosquitoHistory)) {
            $this->mosquitoHistories->add($mosquitoHistory);
            $mosquitoHistory->setUser($this);
        }

        return $this;
    }

    public function removeMosquitoHistory(MosquitoHistory $mosquitoHistory): static
    {
        if ($this->mosquitoHistories->removeElement($mosquitoHistory)) {
            // set the owning side to null (unless already changed)
            if ($mosquitoHistory->getUser() === $this) {
                $mosquitoHistory->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DetailHistory>
     */
    public function getDetailHistories(): Collection
    {
        return $this->detailHistories;
    }

    public function addDetailHistory(DetailHistory $detailHistory): static
    {
        if (!$this->detailHistories->contains($detailHistory)) {
            $this->detailHistories->add($detailHistory);
            $detailHistory->setUser($this);
        }

        return $this;
    }

    public function removeDetailHistory(DetailHistory $detailHistory): static
    {
        if ($this->detailHistories->removeElement($detailHistory)) {
            // set the owning side to null (unless already changed)
            if ($detailHistory->getUser() === $this) {
                $detailHistory->setUser(null);
            }
        }

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    
}
