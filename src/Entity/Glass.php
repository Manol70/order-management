<?php

namespace App\Entity;

use App\Repository\GlassRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GlassRepository::class)]
class Glass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'glass', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\OneToMany(mappedBy: 'glass', targetEntity: GlassHistory::class)]
    private Collection $glassHistories;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->glassHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
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
            $order->setGlass($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getGlass() === $this) {
                $order->setGlass(null);
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
            $glassHistory->setGlass($this);
        }

        return $this;
    }

    public function removeGlassHistory(GlassHistory $glassHistory): static
    {
        if ($this->glassHistories->removeElement($glassHistory)) {
            // set the owning side to null (unless already changed)
            if ($glassHistory->getGlass() === $this) {
                $glassHistory->setGlass(null);
            }
        }

        return $this;
    }
}
