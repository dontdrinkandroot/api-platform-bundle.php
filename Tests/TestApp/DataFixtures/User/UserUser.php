<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\DataFixtures\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;

class UserUser extends Fixture
{
    public const USERNAME = 'user';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername(self::USERNAME);
        $user->setPassword(self::USERNAME);
        $manager->persist($user);
        $manager->flush();
        $this->addReference(self::class, $user);
    }
}
