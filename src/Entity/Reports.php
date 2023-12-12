<?php

namespace App\Entity;

use App\Repository\ReportsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportsRepository::class)]
class Reports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'getReports')]
    private ?Comments $comment_id = null;

    #[ORM\ManyToOne(inversedBy: 'getReports')]
    private ?Blog $blog_id = null;

    #[ORM\ManyToOne(inversedBy: 'getReports')]
    private ?User $person_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getCommentId(): ?Comments
    {
        return $this->comment_id;
    }

    public function setCommentId(?Comments $comment_id): static
    {
        $this->comment_id = $comment_id;

        return $this;
    }

    public function getBlogId(): ?Blog
    {
        return $this->blog_id;
    }

    public function setBlogId(?Blog $blog_id): static
    {
        $this->blog_id = $blog_id;

        return $this;
    }

    public function getPersonId(): ?User
    {
        return $this->person_id;
    }

    public function setPersonId(?User $person_id): static
    {
        $this->person_id = $person_id;

        return $this;
    }
}
