<?php 

namespace App\Service;

use App\Entity\Blacklist;
use Doctrine\ORM\EntityManagerInterface;

class BlacklistService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getBlacklistEmails(): array
    {
        $repository = $this->entityManager->getRepository(Blacklist::class);
        $blacklist = $repository->findAll();

        return array_map(fn($emailAddress) => $emailAddress->getEmailAddress(), $blacklist);
    }

    public function isBanned(string $text): bool
    {
        $emails = $this->getBlacklistEmails();
        $emails = explode(' ', $text);
        $emails = array_map('strtolower', $emails);

        foreach ($emails as $email) {
            $lowercaseEmail = strtolower($email);
            if (in_array($lowercaseEmail, $emails)) {
                return true;
            }
        }
        return false;
    }
}