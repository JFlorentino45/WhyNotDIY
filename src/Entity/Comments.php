<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column]
    private ?bool $edited = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $editedAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Blog $blog = null;

    #[ORM\OneToMany(mappedBy: 'comment_id', targetEntity: ReportsC::class)]
    private Collection $reports;

    #[ORM\Column(nullable: false)]
    private ?bool $hidden = null;

    #[ORM\Column(nullable: false)]
    private ?bool $verified = null;

    public function __construct()
    {
        $this->reports = new ArrayCollection();
    }

    public function getType(): string {
        return 'comment';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

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

    public function isModified(Comments $original): bool
    {
        return $original->getText() !== $this->getText();
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

    /**
     * @return Collection<int, ReportsC>
     */
    public function getCReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(ReportsC $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setCommentId($this);
        }

        return $this;
    }

    public function removeReport(ReportsC $report): static
    {
        if ($this->reports->removeElement($report)) {
            if ($report->getCommentId() === $this) {
                $report->setCommentId(null);
            }
        }

        return $this;
    }

    public function isReportedByUser(User $user): bool
    {
        foreach ($this->reports as $report) {
        if ($report->getReporterId() === $user) {
            return true;
        }
    }

        return false;
    }

    public function getReportsCount(): int
    {
        return $this->reports->count();
    }

    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(?bool $hidden): static
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(?bool $verified): static
    {
        $this->verified = $verified;

        return $this;
    }
}
