<?php


namespace App\Entity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $userName = null;

    #[ORM\Column(length: 255)]
    private ?string $emailAddress = null;

    #[ORM\Column(length: 30)]
    private ?string $role = null;

    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $passwordHash = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): static
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): static
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }
    public function getRoles(): array
{
    // Usually roles are stored as an array; however, since you've modeled it as a string, 
    // we return it inside an array. You may want to modify this approach if you plan 
    // to support multiple roles for a user in the future.
    return [$this->role];
}

public function getSalt(): ?string
{
    // If you're using bcrypt or argon2i/hashing algorithm, then you don't need a separate salt.
    // It's included in the hash itself. Return null in that case.
    return null;
}

public function eraseCredentials(): void
{
    // If you store any temporary authentication-related information on the entity, clear it here.
    $this->plainPassword = null;
}

public function getUserIdentifier(): string
{
    // This can be the same as your getUsername() or getEmailAddress() method, depending on your needs.
    return $this->emailAddress;
}
}