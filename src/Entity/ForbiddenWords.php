<?php

namespace App\Entity;

use App\Repository\ForbiddenWordsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForbiddenWordsRepository::class)]
class ForbiddenWords
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $words = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWords(): ?string
    {
        return $this->words;
    }

    public function setWords(string $words): static
    {
        $this->words = $words;

        return $this;
    }
}
