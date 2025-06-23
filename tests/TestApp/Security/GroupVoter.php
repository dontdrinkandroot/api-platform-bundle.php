<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Security;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\Group;
use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;
use Dontdrinkandroot\Common\CrudOperation;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<'READ',Group>
 */
class GroupVoter extends Voter
{
    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return CrudOperation::READ->value === $attribute && $subject instanceof Group;
    }

    #[Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!($user = $token->getUser()) instanceof User) {
            return false;
        }

        return $user->groups->contains($subject);
    }
}
