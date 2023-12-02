<?php

namespace App\Entity;

use App\Repository\AdminNotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminNotificationRepository::class)]
class AdminNotification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $text = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?Comments $Comment = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?Blog $blog = null;

    #[ORM\Column(type: Types::JSON)]
    private array $words = [];

    public function getId(): ?int
    {
        return $this->id;
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

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

    public function getComment(): ?Comments
    {
        return $this->Comment;
    }

    public function setComment(?Comments $Comment): static
    {
        $this->Comment = $Comment;

        return $this;
    }

    public function getBlog(): ?Blog
    {
        return $this->blog;
    }

    public function setBlog(?Blog $blog): static
    {
        $this->blog = $blog;

        return $this;
    }

    public function getWords(): array
    {
        return $this->words;
    }

    public function setWords(array $words): static
    {
        $this->words = $words;

        return $this;
    }
}
