<?php

namespace App\Entity;

use App\Repository\MosquitoHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: MosquitoHistoryRepository::class)]
class MosquitoHistory
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'mosquitoHistories')]
    private ?Mosquito $mosquito = null;

    #[ORM\ManyToOne(inversedBy: 'mosquitoHistories')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'mosquitoHistories')]
    private ?Order $_order = null;

    #[ORM\Column(nullable: true)]
    private ?int $number_order = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->_order;
    }

    public function setOrder(?Order $_order): static
    {
        $this->_order = $_order;

        return $this;
    }

    public function getNumberOrder(): ?int
    {
        return $this->number_order;
    }

    public function setNumberOrder(?int $number_order): static
    {
        $this->number_order = $number_order;

        return $this;
    }
}
