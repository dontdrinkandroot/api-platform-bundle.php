<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Security;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\ExampleEntity;
use Dontdrinkandroot\Common\CrudOperation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ExampleEntityVoter extends Voter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return is_a($subject, ExampleEntity::class, true)
            && in_array($attribute, CrudOperation::all());
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            CrudOperation::CREATE, CrudOperation::UPDATE => in_array('ROLE_ADMIN', $token->getRoleNames(), true),
            CrudOperation::READ => null !== $token->getUser(),
            default => false,
        };
    }
}
