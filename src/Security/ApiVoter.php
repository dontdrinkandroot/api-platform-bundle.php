<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Dontdrinkandroot\Common\Asserted;
use Override;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter that simplifies Api Voting Operations.
 * @extends Voter<'ddr_api_access',RequestEvent>
 */
abstract class ApiVoter extends Voter
{
    public const string SECURITY_ATTRIBUTE = 'ddr_api_access';

    #[Override]
    protected function supports(string $attribute, mixed $subject): bool
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

    #[Override]
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $apiAttributes = new ApiRequest(Asserted::instanceOf($subject, RequestEvent::class)->getRequest());

        return $this->isOperationGranted($apiAttributes, $subject, $token);
    }

    protected function getQueryParameter(RequestEvent $event, string $name): string|int|float|bool|null
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
