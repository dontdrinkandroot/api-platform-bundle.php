<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

use ApiPlatform\Core\EventListener\EventPriorities;
use Dontdrinkandroot\ApiPlatformBundle\Request\ApiRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Calls Access Decision listeners for all api resources.
 *
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class AccessControlSubscriber implements EventSubscriberInterface
{
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => ['performAccessControl', EventPriorities::POST_DESERIALIZE]];
    }

    public function performAccessControl(RequestEvent $event)
    {
        if (
            $event->getRequest()->attributes->has(ApiRequest::ATTRIBUTE_API_RESOURCE_CLASS)
            && !$this->authorizationChecker->isGranted(ApiVoter::SECURITY_ATTRIBUTE, $event)
        ) {
            throw new AccessDeniedException();
        }
    }
}
