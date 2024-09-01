<?php

namespace App\Entity;

use App\Repository\GlassHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;


#[ORM\Entity(repositoryClass: GlassHistoryRepository::class)]
class GlassHistory
{
    use TimestampableEntity;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $glass_id = null;

    #[ORM\Column]
    private ?int $number_order = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'glassHistories')]
    private ?Order $_order = null;

    #[ORM\ManyToOne(inversedBy: 'glassHistories')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'glassHistories')]
    private ?Glass $glass = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGlassId(): ?int
    {
        return $this->glass_id;
    }

    public function setGlassId(int $glass_id): static
    {
        $this->glass_id = $glass_id;

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

    public function getOrder(): ?Order
    {
        return $this->_order;
    }

    public function setOrder(?Order $_order): static
    {
        $this->_order = $_order;

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
}
