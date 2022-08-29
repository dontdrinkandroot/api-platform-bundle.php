<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

use ApiPlatform\Core\EventListener\EventPriorities;
use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Calls Access Decision listeners for all api resources.
 */
class AccessControlSubscriber implements EventSubscriberInterface
{
    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['performAccessControl', EventPriorities::POST_DESERIALIZE],
            ],
        ];
    }

    public function performAccessControl(RequestEvent $event): void
    {
        if (
            $event->getRequest()->attributes->has(ApiRequest::ATTRIBUTE_API_RESOURCE_CLASS)
            && !$this->authorizationChecker->isGranted(ApiVoter::SECURITY_ATTRIBUTE, $event)
        ) {
            throw new AccessDeniedException();
        }
    }
}
