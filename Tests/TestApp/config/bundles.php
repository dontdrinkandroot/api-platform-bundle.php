<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class           => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class            => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class             => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class                     => ['all' => true],
    ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class    => ['test' => true],
    Liip\TestFixturesBundle\LiipTestFixturesBundle::class           => ['test' => true],
    Dontdrinkandroot\ApiPlatformBundle\DdrApiPlatformBundle::class  => ['all' => true],
];
