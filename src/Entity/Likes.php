<?php

namespace App\Entity;

use App\Repository\LikesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikesRepository::class)]
class Likes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $UserId = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?Blog $BlogId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->UserId;
    }

    public function setUserId(?User $UserId): static
    {
        $this->UserId = $UserId;

        return $this;
    }

    public function getBlogId(): ?Blog
    {
        return $this->BlogId;
    }

    public function setBlogId(?Blog $BlogId): static
    {
        $this->BlogId = $BlogId;

        return $this;
    }
}
