<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Security;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;
use Dontdrinkandroot\Common\CrudOperation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserClassVoter extends Voter
{
    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, $subject): bool
    {
        $crudOperation = CrudOperation::tryFrom($attribute);
        return is_a($subject, User::class, true)
            && CrudOperation::READ === $crudOperation;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $crudOperation = CrudOperation::tryFrom($attribute);
        return match ($crudOperation) {
            CrudOperation::READ => null !== $token->getUser(),
            default => false,
        };
    }
}
