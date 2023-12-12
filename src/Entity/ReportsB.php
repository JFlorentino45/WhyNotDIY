<?php

namespace App\Entity;

use App\Repository\ReportsBRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportsBRepository::class)]
class ReportsB
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $reporter_id = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Blog $blog_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReporterId(): ?User
    {
        return $this->reporter_id;
    }

    public function setReporterId(?User $reporter_id): static
    {
        $this->reporter_id = $reporter_id;

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
}
