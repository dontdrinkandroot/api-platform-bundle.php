<?php

namespace Dontdrinkandroot\ApiPlatformBundle;

use Override;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DdrApiPlatformBundle extends Bundle
{
    #[Override]
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
