<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Repository\GroupRepository;
use RuntimeException;

#[ApiResource]
#[ORM\Entity(repositoryClass: GroupRepository::class)]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'groups')]
    public Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id ?? throw new RuntimeException('Entity is not persisted');
    }
}
