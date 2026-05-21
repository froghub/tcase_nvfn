<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\Status;
use App\Repository\PhoneNumberRepository;
use App\State\PhoneNumberProcessor;
use App\State\PhoneNumberProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: PhoneNumberRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(denormalizationContext: ['groups' => ['phone:create']],),
        new Patch(denormalizationContext: ['groups' => ['phone:update']],),
    ],
    normalizationContext: ['groups' => ['phone:read']],
    paginationClientItemsPerPage: true,
    paginationMaximumItemsPerPage: 100,
    provider: PhoneNumberProvider::class,
    processor: PhoneNumberProcessor::class
)]
#[ApiFilter(SearchFilter::class, properties: ['status'=>'exact', 'tariff'=>'partial'])]
class PhoneNumber
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[Groups(['phone:read'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 15, unique: true)]
    #[Groups(['phone:create','phone:read'])]
    #[Assert\NotBlank(message: 'Номер обязателен')]
    #[Assert\Length(min:5, max:15)]
    #[Assert\Regex(
        pattern: '/^[0-9]+$/',
        message: 'Допустимы только числа'
    )]
    private ?string $number = null;

    #[ORM\Column(enumType: Status::class)]
    #[Groups(['phone:read','phone:update'])]
    private ?Status $status = Status::ACTIVE;

    #[ORM\Column(length: 255)]
    #[Groups(['phone:create','phone:read','phone:update'])]
    #[Assert\NotBlank(message: 'Тариф обязателен')]
    private ?string $tariff = null;

    #[ORM\Column]
    #[Groups(['phone:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['phone:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTariff(): ?string
    {
        return $this->tariff;
    }

    public function setTariff(string $tariff): static
    {
        $this->tariff = $tariff;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): static
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
