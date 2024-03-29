<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
#[ORM\Table(indexes: [new ORM\Index(columns: ['title', 'text'], flags: ['fulltext'])])]

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
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $edited = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $editedAt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $createdBy = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $category = null;
    
    #[ORM\Column(nullable: false)]
    private ?bool $hidden = null;
    
    #[ORM\Column(nullable: false)]
    private ?bool $verified = null;

    #[ORM\OneToMany(mappedBy: 'blog_id', targetEntity: ReportsB::class)]
    private Collection $reports;
    
    #[ORM\OneToMany(mappedBy: 'BlogId', targetEntity: Likes::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'blog', targetEntity: Comments::class, cascade: ['persist', 'remove'])]
    private Collection $comments;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    public function getType(): string {
        return 'blog';
    }

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


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
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

    public function isModified(Blog $original): bool
    {
        return $original->getTitle() !== $this->getTitle()
            || $original->getVideoUrl() !== $this->getVideoUrl()
            || $original->getText() !== $this->getText()
            || $original->getCategory() !== $this->getCategory();
    }

    /**
     * @return Collection<int, Likes>
     */

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Likes $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setBlogId($this);
        }

        return $this;
    }

    public function removeLike(Likes $like): static
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getBlogId() === $this) {
                $like->setBlogId(null);
            }
        }

        return $this;
    }

    public function isLikedByUser(User $user): bool
    {
        foreach ($this->likes as $like) {
        if ($like->getUserId() === $user) {
            return true;
        }
    }

        return false;
    }

    public function getLikesCount(): int
    {
        return $this->likes->count();
    }

    public function getCommentsCount(): int
    {
        return $this->comments->count();
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, ReportsB>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(ReportsB $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setBlogId($this);
        }

        return $this;
    }

    public function removeReport(ReportsB $report): static
    {
        if ($this->reports->removeElement($report)) {
            if ($report->getBlogId() === $this) {
                $report->setBlogId(null);
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
