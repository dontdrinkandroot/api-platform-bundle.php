<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\Acceptance;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class ExampleEntityEndpointTest extends AbstractAcceptanceTest
{
    public function testPostUnauthorized()
    {
        $this->loadKernelBrowserAndFixtures([]);
        $response = $this->jsonPost('/example_entities', [], [], []);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testPostForbidden()
    {
        $this->loadKernelBrowserAndFixtures([]);
        $response = $this->jsonPost('/example_entities', [], $this->addBasicAuthorizationHeader('user', 'user'), []);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testPost()
    {
        $this->loadKernelBrowserAndFixtures([]);
        $response = $this->jsonPost('/example_entities', [], $this->addBasicAuthorizationHeader('admin', 'admin'), []);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }
}
