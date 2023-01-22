<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Department;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Department;

class DepartmentManagement extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $department = new Department('Management');
        $manager->persist($department);
        $manager->flush();
        $this->addReference(self::class, $department);
    }
}
