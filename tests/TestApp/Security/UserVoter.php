<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Security;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\User;
use Dontdrinkandroot\Common\CrudOperation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        $crudOperation = CrudOperation::tryFrom($attribute);
        return is_a($subject, User::class, true)
            && in_array($crudOperation, CrudOperation::all());
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $crudOperation = CrudOperation::tryFrom($attribute);
        return match ($crudOperation) {
            CrudOperation::CREATE, CrudOperation::UPDATE => in_array('ROLE_ADMIN', $token->getRoleNames(), true),
            CrudOperation::READ => null !== $token->getUser(),
            default => false,
        };
    }
}
