<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $number_order = null;

    #[ORM\Column]
    private ?int $paid = null;

    #[ORM\Column(length: 255)]
    private ?string $document = null;

    #[ORM\Column(length: 50)]
    private ?string $number_doc = null;

    #[ORM\ManyToOne]
    private ?Customer $customer = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?TypeMontage $type_montage = null;

    #[ORM\ManyToOne]
    private ?Order $_order = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPaid(): ?int
    {
        return $this->paid;
    }

    public function setPaid(int $paid): static
    {
        $this->paid = $paid;

        return $this;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(string $document): static
    {
        $this->document = $document;

        return $this;
    }

    public function getNumberDoc(): ?string
    {
        return $this->number_doc;
    }

    public function setNumberDoc(string $number_doc): static
    {
        $this->number_doc = $number_doc;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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
