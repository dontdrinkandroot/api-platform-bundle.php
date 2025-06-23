<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Security;

use Override;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AdminVoter implements VoterInterface
{
    #[Override]
    public function vote(TokenInterface $token, mixed $subject, array $attributes)
    {
        if (in_array('ROLE_ADMIN', $token->getRoleNames(), true)) {
            return Voter::ACCESS_GRANTED;
        }

        return Voter::ACCESS_ABSTAIN;
    }
}
