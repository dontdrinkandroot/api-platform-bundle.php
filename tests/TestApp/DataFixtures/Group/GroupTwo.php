<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Group;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserTwo;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Group;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;
use Override;

class GroupTwo extends Fixture implements DependentFixtureInterface
{
    #[Override]
    public function getDependencies(): array
    {
        return [UserTwo::class];
    }

    #[Override]
    public function load(ObjectManager $manager): void
    {
        $group = new Group();
        $group->users->add($this->getReference(UserTwo::class, User::class));
        $manager->persist($group);
        $manager->flush();
        $this->addReference(self::class, $group);
    }
}
