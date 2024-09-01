<?php

namespace App\Entity;

use App\Repository\DetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailRepository::class)]
class Detail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'detail', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\OneToMany(mappedBy: 'detail', targetEntity: DetailHistory::class)]
    private Collection $detailHistories;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->detailHistories = new ArrayCollection();
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
            $order->setDetail($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getDetail() === $this) {
                $order->setDetail(null);
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
            $detailHistory->setDetail($this);
        }

        return $this;
    }

    public function removeDetailHistory(DetailHistory $detailHistory): static
    {
        if ($this->detailHistories->removeElement($detailHistory)) {
            // set the owning side to null (unless already changed)
            if ($detailHistory->getDetail() === $this) {
                $detailHistory->setDetail(null);
            }
        }

        return $this;
    }
}
