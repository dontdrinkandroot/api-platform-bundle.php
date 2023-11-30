<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Department\DepartmentManagement;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Department;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;

class UserAdmin extends Fixture implements DependentFixtureInterface
{
    final public const USERNAME = 'admin';
    final public const PASSWORD = self::USERNAME;

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [DepartmentManagement::class];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User(
            self::USERNAME,
            self::PASSWORD,
            $this->getReference(DepartmentManagement::class, Department::class),
            true
        );
        $manager->persist($user);
        $manager->flush();
        $this->addReference(self::class, $user);
    }
}
