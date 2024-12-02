<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

class Users extends Fixture implements DependentFixtureInterface
{
    #[Override]
    public function getDependencies(): array
    {
        return [UserAdmin::class, UserOne::class];
    }

    #[Override]
    public function load(ObjectManager $manager): void
    {
        /* Noop */
    }
}
