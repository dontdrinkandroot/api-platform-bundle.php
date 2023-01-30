<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Dontdrinkandroot\Common\Asserted;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;

class OutputRoutesTest extends WebTestCase
{
    public function testRoutes(): void
    {
//        $this->markTestSkipped('Useful test for debugging');

        $router = Asserted::instanceOf(self::getContainer()->get(RouterInterface::class), RouterInterface::class);
        self::assertNotEmpty($router->getRouteCollection());

        foreach ($router->getRouteCollection()->all() as $name => $route) {
            echo sprintf(
                "[name: %s, methods: %s, path: %s, controller: %s]%s",
                $name,
                implode(',', $route->getMethods()),
                $route->getPath(),
                $route->getDefault('_controller'),
                PHP_EOL
            );
        }
    }
}
