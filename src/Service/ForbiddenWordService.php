<?php 

namespace App\Service;

use App\Entity\ForbiddenWords;
use Doctrine\ORM\EntityManagerInterface;

class ForbiddenWordService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getForbiddenWords(): array
    {
        $repository = $this->entityManager->getRepository(ForbiddenWords::class);
        $forbiddenWords = $repository->findAll();

        return array_map(fn($word) => $word->getWords(), $forbiddenWords);
    }

    public function isForbidden(string $text): bool
    {
        $forbiddenWords = $this->getForbiddenWords();
        $lowercaseWord = strtolower($text);
        if (in_array($lowercaseWord, $forbiddenWords)) {
            return true;
        } else {
            return false;
        }
    }

    public function containsForbiddenWord(string $text): bool
    {
        $forbiddenWords = $this->getForbiddenWords();
        $lowercaseWord = strtolower($text);

        foreach ($forbiddenWords as $word) {
            if (strpos($lowercaseWord, $word) !== false) {
                return true;
            }
        }

        return false;
    }
}