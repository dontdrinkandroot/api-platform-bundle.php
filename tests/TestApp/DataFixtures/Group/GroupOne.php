<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\Group;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserAdmin;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User\UserOne;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Group;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;

class GroupOne extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [UserAdmin::class, UserOne::class];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $group = new Group();
        $group->users->add($this->getReference(UserAdmin::class, User::class));
        $group->users->add($this->getReference(UserOne::class, User::class));
        $manager->persist($group);
        $manager->flush();
        $this->addReference(self::class, $group);
    }
}
