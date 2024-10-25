<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Exception\PersisterException;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;


use PHPUnit\TextUI\XmlConfiguration\RemoveEmptyFilter;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]

class Order
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $number = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    #[Assert\Type('float')]
    #[Assert\PositiveOrZero]
    private ?float $quadrature = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(value: 'today', message: 'The date must be today or in the future.')]
   /* #[Assert\Date(groups: ["create"])]//тези 2 реда се използват, ако искаме да валидираме датата и при edit
    #[Assert\GreaterThanOrEqual("today", groups: ["create"])] */
    private ?\DateTimeInterface $for_date = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    #[Assert\Type('float')]
    #[Assert\PositiveOrZero]
    private ?float $price = null;

    #[ORM\Column]
    private ?float $paid = null;


    #[ORM\Column(length: 50, nullable: true)]
    private ?string $scheme = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(nullable: true)]
    private ?bool $status_mail = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Type $type = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeMontage $type_montage = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Glass $glass = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Detail $detail = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Mosquito $mosquito = null;

    #[ORM\OneToMany(mappedBy: '_order', targetEntity: GlassHistory::class)]
    private Collection $glassHistories;

    #[ORM\OneToMany(mappedBy: '_order', targetEntity: StatusHistory::class)]
    private Collection $statusHistories;

    #[ORM\OneToMany(mappedBy: '_order', targetEntity: MosquitoHistory::class)]
    private Collection $mosquitoHistories;

    #[ORM\OneToMany(mappedBy: '_order', targetEntity: DetailHistory::class)]
    private Collection $detailHistories;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: false)]
    private float $balance = 0.00;

    public function __construct()
    {
        $this->glassHistories = new ArrayCollection();
        $this->statusHistories = new ArrayCollection();
        $this->mosquitoHistories = new ArrayCollection();
        $this->detailHistories = new ArrayCollection();
    }
   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getQuadrature(): ?float
    {
        return $this->quadrature;
    }

    public function setQuadrature(float $quadrature): static
    {
        $this->quadrature = $quadrature;

        return $this;
    }

    public function getForDate(): ?\DateTimeInterface
    {
        return $this->for_date;
    }

    public function setForDate(\DateTimeInterface $for_date): static
    {
        $this->for_date = $for_date;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        $this->updateBalance(); // Актуализирай баланса, ако се промени цената

        return $this;
    }

    public function getPaid(): ?float
    {
        return $this->paid;
    }

    public function setPaid(float $paid): static
    {
        $this->paid = $paid;
        $this->updateBalance(); // Актуализирай баланса при всяко плащане

        return $this;
    }

    private function updateBalance(): void
    {
        $this->balance = $this->price - $this->paid;
    }


    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function setScheme(?string $scheme): static
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function isStatusMail(): ?bool
    {
        return $this->status_mail;
    }

    public function setStatusMail(?bool $status_mail): static
    {
        $this->status_mail = $status_mail;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

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

    public function getTypeMontage(): ?TypeMontage
    {
        return $this->type_montage;
    }

    public function setTypeMontage(?TypeMontage $type_montage): static
    {
        $this->type_montage = $type_montage;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getGlass(): ?Glass
    {
        return $this->glass;
    }

    public function setGlass(?Glass $glass): static
    {
        $this->glass = $glass;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDetail(): ?Detail
    {
        return $this->detail;
    }

    public function setDetail(?Detail $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getMosquito(): ?Mosquito
    {
        return $this->mosquito;
    }

    public function setMosquito(?Mosquito $mosquito): static
    {
        $this->mosquito = $mosquito;

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
            $glassHistory->setOrder($this);
        }

        return $this;
    }

    public function removeGlassHistory(GlassHistory $glassHistory): static
    {
        if ($this->glassHistories->removeElement($glassHistory)) {
            // set the owning side to null (unless already changed)
            if ($glassHistory->getOrder() === $this) {
                $glassHistory->setOrder(null);
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
            $statusHistory->setOrder($this);
        }

        return $this;
    }

    public function removeStatusHistory(StatusHistory $statusHistory): static
    {
        if ($this->statusHistories->removeElement($statusHistory)) {
            // set the owning side to null (unless already changed)
            if ($statusHistory->getOrder() === $this) {
                $statusHistory->setOrder(null);
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
            $mosquitoHistory->setOrder($this);
        }

        return $this;
    }

    public function removeMosquitoHistory(MosquitoHistory $mosquitoHistory): static
    {
        if ($this->mosquitoHistories->removeElement($mosquitoHistory)) {
            // set the owning side to null (unless already changed)
            if ($mosquitoHistory->getOrder() === $this) {
                $mosquitoHistory->setOrder(null);
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
            $detailHistory->setOrder($this);
        }

        return $this;
    }

    public function removeDetailHistory(DetailHistory $detailHistory): static
    {
        if ($this->detailHistories->removeElement($detailHistory)) {
            // set the owning side to null (unless already changed)
            if ($detailHistory->getOrder() === $this) {
                $detailHistory->setOrder(null);
            }
        }

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }
//Данните се сетват автоматично при промяна на price и paid  
/*    public function setBalance(?float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }*/
    
}
