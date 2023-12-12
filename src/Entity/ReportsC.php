<?php

namespace App\Entity;

use App\Repository\ReportsCRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportsCRepository::class)]
class ReportsC
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
    private ?Comments $comment_id = null;

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

    public function getCommentId(): ?Comments
    {
        return $this->comment_id;
    }

    public function setCommentId(?Comments $comment_id): static
    {
        $this->comment_id = $comment_id;

        return $this;
    }
}
