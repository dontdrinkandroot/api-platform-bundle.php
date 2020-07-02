<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class CrudAction
{
    const LIST = 'LIST';
    const CREATE = 'CREATE';
    const READ = 'READ';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';

    public static function all(): array
    {
        return [self::LIST, self::CREATE, self::READ, self::UPDATE, self::DELETE];
    }
}
