<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Dontdrinkandroot\ApiPlatformBundle\Tests\ApiTestTrait;
use Dontdrinkandroot\Common\Asserted;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractAcceptanceTest extends WebTestCase
{
    use ApiTestTrait;

    protected ReferenceRepository $referenceRepository;

    protected KernelBrowser $client;

    /**
     * {@inheritdoc}
     */
    protected function getApiClient(): KernelBrowser
    {
        return $this->client;
    }

    protected function loadKernelBrowserAndFixtures(array $classNames = []): ReferenceRepository
    {
        $this->client = self::createClient();
        $databaseToolCollection = Asserted::instanceOf(
            self::getContainer()->get(DatabaseToolCollection::class),
            DatabaseToolCollection::class
        );
        $this->referenceRepository = $databaseToolCollection->get()
            ->loadFixtures($classNames)
            ->getReferenceRepository();

        return $this->referenceRepository;
    }

    /**
     * @template T
     * @param string $referenceName
     * @param class-string<T> $className
     *
     * @return T
     */
    protected function getReference(string $referenceName, string $className): object
    {
        return $this->referenceRepository->getReference($referenceName, $className);
    }
}
