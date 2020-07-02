<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
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

        if (!$subject instanceof RequestEvent) {
            return false;
        }

        $apiAttributes = new ApiRequest($subject->getRequest());

        return $this->supportsOperation($apiAttributes, $subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        assert($subject instanceof RequestEvent);
        $apiAttributes = new ApiRequest($subject->getRequest());

        return $this->isOperationGranted(
            $apiAttributes,
            $subject,
            $token
        );
    }

    protected function getQueryParameter(RequestEvent $event, string $name)
    {
        return $event->getRequest()->query->get($name);
    }

    protected abstract function supportsOperation(ApiRequest $apiRequest, RequestEvent $event): bool;

    protected abstract function isOperationGranted(
        ApiRequest $apiAttributes,
        RequestEvent $event,
        TokenInterface $token
    ): bool;
}
