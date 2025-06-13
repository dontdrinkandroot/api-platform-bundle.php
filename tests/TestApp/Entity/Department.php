<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Repository\DepartmentRepository;
use RuntimeException;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['department:list', 'department:read'])]
    private ?int $id = null;

    /** @var Collection<array-key,User> */
    #[ORM\OneToMany(mappedBy: 'department', targetEntity: User::class)]
    public Collection $users;

    public function __construct(
        #[Assert\NotBlank]
        #[ORM\Column(type: Types::STRING, nullable: false)]
        #[Groups(['department:create', 'department:list', 'department:read'])]
        public string $name
    ) {
        $this->users = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id ?? throw new RuntimeException('Entity is not persisted');
    }
}
