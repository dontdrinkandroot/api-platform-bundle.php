<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Repository\DepartmentRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'department')]
    public Collection $users;

    public function __construct(
        #[Assert\NotBlank]
        #[ORM\Column(type: Types::STRING, nullable: false)]
        public string $name
    ) {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
