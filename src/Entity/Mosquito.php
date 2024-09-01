<?php

namespace App\Entity;

use App\Repository\MosquitoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MosquitoRepository::class)]
class Mosquito
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'mosquito', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\OneToMany(mappedBy: 'mosquito', targetEntity: MosquitoHistory::class)]
    private Collection $mosquitoHistories;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->mosquitoHistories = new ArrayCollection();
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
            $order->setMosquito($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getMosquito() === $this) {
                $order->setMosquito(null);
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
            $mosquitoHistory->setMosquito($this);
        }

        return $this;
    }

    public function removeMosquitoHistory(MosquitoHistory $mosquitoHistory): static
    {
        if ($this->mosquitoHistories->removeElement($mosquitoHistory)) {
            // set the owning side to null (unless already changed)
            if ($mosquitoHistory->getMosquito() === $this) {
                $mosquitoHistory->setMosquito(null);
            }
        }

        return $this;
    }
}
