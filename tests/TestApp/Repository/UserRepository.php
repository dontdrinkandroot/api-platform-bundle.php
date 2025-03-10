<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
}
