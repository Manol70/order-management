<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: StatusHistory::class)]
    private Collection $statusHistories;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->statusHistories = new ArrayCollection();
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
            $order->setStatus($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getStatus() === $this) {
                $order->setStatus(null);
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
            $statusHistory->setStatus($this);
        }

        return $this;
    }

    public function removeStatusHistory(StatusHistory $statusHistory): static
    {
        if ($this->statusHistories->removeElement($statusHistory)) {
            // set the owning side to null (unless already changed)
            if ($statusHistory->getStatus() === $this) {
                $statusHistory->setStatus(null);
            }
        }

        return $this;
    }
}
