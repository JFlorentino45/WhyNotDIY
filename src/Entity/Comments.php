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

    #[ORM\OneToMany(mappedBy: 'comment_id', targetEntity: Reports::class)]
    private Collection $getReports;

    public function __construct()
    {
        $this->getReports = new ArrayCollection();
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
     * @return Collection<int, Reports>
     */
    public function getGetReports(): Collection
    {
        return $this->getReports;
    }

    public function addGetReport(Reports $getReport): static
    {
        if (!$this->getReports->contains($getReport)) {
            $this->getReports->add($getReport);
            $getReport->setCommentId($this);
        }

        return $this;
    }

    public function removeGetReport(Reports $getReport): static
    {
        if ($this->getReports->removeElement($getReport)) {
            // set the owning side to null (unless already changed)
            if ($getReport->getCommentId() === $this) {
                $getReport->setCommentId(null);
            }
        }

        return $this;
    }
}
