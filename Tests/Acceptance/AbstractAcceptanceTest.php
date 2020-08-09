<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Dontdrinkandroot\ApiPlatformBundle\Tests\ApiTestTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
abstract class AbstractAcceptanceTest extends WebTestCase
{
    use FixturesTrait;

    use ApiTestTrait;

    protected ReferenceRepository $referenceRepository;

    protected KernelBrowser $kernelBrowser;

    /**
     * {@inheritdoc}
     */
    protected function getApiTestKernelBrowser(): KernelBrowser
    {
        return $this->kernelBrowser;
    }

    protected function loadKernelBrowserAndFixtures(array $classNames = []): ReferenceRepository
    {
        $this->kernelBrowser = self::createClient();
        $this->referenceRepository = $this->loadFixtures($classNames)->getReferenceRepository();

        return $this->referenceRepository;
    }
}
