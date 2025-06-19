<?php

namespace App\Entity;

use App\Repository\DevisPrestationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DevisPrestationRepository::class)]
#[ORM\Table(name: '`devis_prestation`')]
#[ORM\HasLifecycleCallbacks]
class DevisPrestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: Types::GUID, unique: true)]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Devis::class, inversedBy: 'prestations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devis $devis = null;

    #[ORM\ManyToOne(targetEntity: Prestation::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prestation $prestation = null;

    #[ORM\Column]
    private ?float $quantity = 1;

    #[ORM\Column]
    private ?float $unitPrice = 0;

    #[ORM\Column]
    private ?float $totalPrice = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?bool $isDiscounted = false;

    #[ORM\Column]
    private ?float $discountRate = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): static
    {
        $this->devis = $devis;
        return $this;
    }

    public function getPrestation(): ?Prestation
    {
        return $this->prestation;
    }

    public function setPrestation(?Prestation $prestation): static
    {
        $this->prestation = $prestation;
        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): static
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function isIsDiscounted(): ?bool
    {
        return $this->isDiscounted;
    }

    public function setIsDiscounted(bool $isDiscounted): static
    {
        $this->isDiscounted = $isDiscounted;
        return $this;
    }

    public function getDiscountRate(): ?float
    {
        return $this->discountRate;
    }

    public function setDiscountRate(float $discountRate): static
    {
        $this->discountRate = $discountRate;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
