<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $videoUrl = null;

    #[ORM\Column]
    private ?int $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $commentId = null;

    #[ORM\Column(nullable: true)]
    private ?int $likedBy = null;

    #[ORM\Column]
    private ?bool $edited = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $editedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function setVideoUrl(string $videoUrl): static
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): static
    {
        $this->createdBy = $createdBy;

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

    public function getCommentId(): ?int
    {
        return $this->commentId;
    }

    public function setCommentId(?int $commentId): static
    {
        $this->commentId = $commentId;

        return $this;
    }

    public function getLikedBy(): ?int
    {
        return $this->likedBy;
    }

    public function setLikedBy(?int $likedBy): static
    {
        $this->likedBy = $likedBy;

        return $this;
    }

    public function isEdited(): ?bool
    {
        return $this->edited;
    }

    public function setEdited(bool $edited): static
    {
        $this->edited = $edited;

        return $this;
    }

    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->editedAt;
    }

    public function setEditedAt(?\DateTimeImmutable $editedAt): static
    {
        $this->editedAt = $editedAt;

        return $this;
    }
}
