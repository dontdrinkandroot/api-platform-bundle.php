<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Department;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Department;
use Override;

class DepartmentAccounting extends Fixture
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $department = new Department('Accounting');
        $manager->persist($department);
        $manager->flush();
        $this->addReference(self::class, $department);
    }
}
