<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Department\DepartmentManagement;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Department;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;
use Override;

class UserAdmin extends Fixture implements DependentFixtureInterface
{
    final public const string USERNAME = 'admin';
    final public const string PASSWORD = self::USERNAME;

    #[Override]
    public function getDependencies(): array
    {
        return [DepartmentManagement::class];
    }

    #[Override]
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
