<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Security;

use Override;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string,mixed>
 */
class AdminVoter extends Voter
{
    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return true;
    }

    #[Override]
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return in_array('ROLE_ADMIN', $token->getRoleNames(), true);
    }
}
