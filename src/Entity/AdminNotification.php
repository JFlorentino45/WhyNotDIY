<?php

namespace App\Entity;

use App\Repository\AdminNotificationRepository;
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

    #[ORM\Column]
    private ?bool $isSignUp = null;

    #[ORM\Column]
    private ?bool $isComment = null;

    #[ORM\Column]
    private ?bool $isBlog = null;

    #[ORM\Column]
    private ?int $identifier = null;

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

    public function isIsSignUp(): ?bool
    {
        return $this->isSignUp;
    }

    public function setIsSignUp(bool $isSignUp): static
    {
        $this->isSignUp = $isSignUp;

        return $this;
    }

    public function isIsComment(): ?bool
    {
        return $this->isComment;
    }

    public function setIsComment(bool $isComment): static
    {
        $this->isComment = $isComment;

        return $this;
    }

    public function isIsBlog(): ?bool
    {
        return $this->isBlog;
    }

    public function setIsBlog(bool $isBlog): static
    {
        $this->isBlog = $isBlog;

        return $this;
    }

    public function getIdentifier(): ?int
    {
        return $this->identifier;
    }

    public function setIdentifier(int $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }
}
