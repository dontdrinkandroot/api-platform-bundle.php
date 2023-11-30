<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Department\DepartmentAccounting;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Department;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;

class UserOne extends Fixture implements DependentFixtureInterface
{
    final public const USERNAME = 'userone';
    final public const PASSWORD = self::USERNAME;

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [DepartmentAccounting::class];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User(
            self::USERNAME,
            self::PASSWORD,
            $this->getReference(DepartmentAccounting::class, Department::class),
        );
        $manager->persist($user);
        $manager->flush();
        $this->addReference(self::class, $user);
    }
}
