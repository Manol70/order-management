<?php

namespace App\Entity;

use App\Repository\StatusHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: StatusHistoryRepository::class)]
class StatusHistory
{
    use TimestampableEntity;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $status_id = null;

    #[ORM\Column]
    private ?int $number_order = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'statusHistories')]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'statusHistories')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'statusHistories')]
    private ?Order $_order = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatusId(): ?int
    {
        return $this->status_id;
    }

    public function setStatusId(int $status_id): static
    {
        $this->status_id = $status_id;

        return $this;
    }

    public function getNumberOrder(): ?int
    {
        return $this->number_order;
    }

    public function setNumberOrder(int $number_order): static
    {
        $this->number_order = $number_order;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

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
}
