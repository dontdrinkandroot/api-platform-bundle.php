<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Security;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;
use Dontdrinkandroot\Common\CrudOperation;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<'READ',User>
 */
class UserClassVoter extends Voter
{
    #[Override]
    protected function supports(string $attribute, $subject): bool
    {
        $crudOperation = CrudOperation::tryFrom($attribute);
        return is_a($subject, User::class)
            && CrudOperation::READ === $crudOperation;
    }

    #[Override]
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return null !== $token->getUser();
    }
}
