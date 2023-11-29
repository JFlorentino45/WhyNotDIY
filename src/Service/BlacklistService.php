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

    public function isBanned(string $input): bool
    {
        $emails = $this->getBlacklistEmails();

        foreach ($emails as $email) {
            $emailLower = strtolower(strip_tags($email));
            $inputLower = strtolower(strip_tags($input));
            if ($inputLower == $emailLower) {
                return true;
            }
        }
        return false;
    }
}