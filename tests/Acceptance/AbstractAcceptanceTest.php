<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Dontdrinkandroot\ApiPlatformBundle\Test\ApiTestTrait;
use Dontdrinkandroot\Common\Asserted;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractAcceptanceTest extends WebTestCase
{
    use ApiTestTrait;

    protected ReferenceRepository $referenceRepository;

    protected KernelBrowser $kernelBrowser;

    /**
     * {@inheritdoc}
     */
    protected function getClient(): KernelBrowser
    {
        return $this->kernelBrowser;
    }

    protected function loadKernelBrowserAndFixtures(array $classNames = []): ReferenceRepository
    {
        $this->kernelBrowser = self::createClient();
        $databaseToolCollection = Asserted::instanceOf(
            self::getContainer()->get(DatabaseToolCollection::class),
            DatabaseToolCollection::class
        );
        $this->referenceRepository = $databaseToolCollection->get()
            ->loadFixtures($classNames)
            ->getReferenceRepository();

        return $this->referenceRepository;
    }
}
