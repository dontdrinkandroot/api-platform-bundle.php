<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

use Dontdrinkandroot\ApiPlatformBundle\ApiPlatform\ApiRequestAttributes;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter that simplifies Api Voting Operations.
 *
 * @author Philip Washington Sorst <philip@sorst.net>
 */
abstract class ApiVoter extends Voter
{
    const SECURITY_ATTRIBUTE = 'ddr_api_access';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if ($attribute !== self::SECURITY_ATTRIBUTE) {
            return false;
        }

        if (!$subject instanceof GetResponseEvent) {
            return false;
        }

        $apiAttributes = ApiRequestAttributes::extract($subject->getRequest());

        return $this->supportsOperation($apiAttributes, $subject);
    }

    /**
     * {@inheritdoc}
     *
     * @param GetResponseEvent $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $apiAttributes = ApiRequestAttributes::extract($subject->getRequest());

        return $this->isOperationGranted(
            $apiAttributes,
            $subject,
            $token
        );
    }

    protected function getQueryParameter(GetResponseEvent $event, string $name)
    {
        return $event->getRequest()->query->get($name);
    }

    protected abstract function supportsOperation(ApiRequestAttributes $apiAttributes, GetResponseEvent $event);

    protected abstract function isOperationGranted(
        ApiRequestAttributes $apiAttributes,
        GetResponseEvent $event,
        TokenInterface $token
    );
}
