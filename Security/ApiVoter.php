<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Dontdrinkandroot\Common\Asserted;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter that simplifies Api Voting Operations.
 */
abstract class ApiVoter extends Voter
{
    const SECURITY_ATTRIBUTE = 'ddr_api_access';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        if ($attribute !== self::SECURITY_ATTRIBUTE) {
            return false;
        }

        if (!$subject instanceof RequestEvent) {
            return false;
        }

        $apiRequest = new ApiRequest($subject->getRequest());

        return $this->supportsOperation($apiRequest, $subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $apiAttributes = new ApiRequest(Asserted::instanceOf($subject, RequestEvent::class)->getRequest());

        return $this->isOperationGranted($apiAttributes, $subject, $token);
    }

    protected function getQueryParameter(RequestEvent $event, string $name)
    {
        return $event->getRequest()->query->get($name);
    }

    protected abstract function supportsOperation(ApiRequest $apiRequest, RequestEvent $event): bool;

    protected abstract function isOperationGranted(
        ApiRequest $apiRequest,
        RequestEvent $event,
        TokenInterface $token
    ): bool;
}
