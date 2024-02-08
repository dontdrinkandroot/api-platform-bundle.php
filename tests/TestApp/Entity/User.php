<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Repository\UserRepository;
use Override;
use RuntimeException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource]
#[ApiResource(
    uriTemplate: '/departments/{departmentId}/users',
    operations: [new GetCollection()],
    uriVariables: [
        'departmentId' => new Link(fromClass: Department::class, toProperty: 'department'),
    ]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'users')]
    public Collection $groups;

    public function __construct(
        #[ORM\Column]
        #[NotBlank]
        private string $username,

        #[ORM\Column]
        #[NotBlank]
        private string $password,

        #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'users')]
        #[ORM\JoinColumn(nullable: false)]
        public Department $department,

        #[ORM\Column]
        private bool $admin = false,
    ) {
        $this->groups = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id ?? throw new RuntimeException('Entity is not persisted');
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        if ($this->admin) {
            $roles[] = 'ROLE_ADMIN';
        }

        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        /* Noop */
    }

    public function isAdmin(): bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): void
    {
        $this->admin = $admin;
    }
}
