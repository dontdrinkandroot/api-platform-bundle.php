<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\ExampleEntity;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class ExampleEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExampleEntity::class);
    }
}
