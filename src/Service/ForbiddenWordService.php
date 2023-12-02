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
        $words = explode(' ', $text);
        $forbiddenWords = array_map('strtolower', $forbiddenWords);

        foreach ($words as $word) {
            $lowercaseWord = strtolower($word);
            if (in_array($lowercaseWord, $forbiddenWords)) {
                return true;
            }
        }
        return false;
    }

    public function containsForbiddenWord(string $text): array
    {
        $forbiddenWords = $this->getForbiddenWords();
        $lowercaseWord = strtolower($text);
        $foundWords = [];

        foreach ($forbiddenWords as $word) {
            if (strpos($lowercaseWord, $word) !== false) {
                $foundWords[] = $word;
            }
        }

        if (!empty($foundWords)) {
        return ['found' => true, 'word' => $foundWords];
        }
        return ['found' => false, 'word' => null];
    }
}