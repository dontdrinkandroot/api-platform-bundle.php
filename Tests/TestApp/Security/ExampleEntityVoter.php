<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Security;

use Dontdrinkandroot\ApiPlatformBundle\Tests\TestApp\Entity\ExampleEntity;
use Dontdrinkandroot\Crud\CrudOperation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class ExampleEntityVoter extends Voter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return is_a($subject, ExampleEntity::class, true) &&
            in_array($attribute, CrudOperation::all());
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        switch ($attribute) {
            case CrudOperation::CREATE:
            case CrudOperation::UPDATE:
                return $token->isAuthenticated() && in_array('ROLE_ADMIN', $token->getRoleNames(), true);
            case CrudOperation::READ:
                return $token->isAuthenticated();
            default:
                return false;
        }
    }
}
