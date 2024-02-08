<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use ApiPlatform\Symfony\Security\ResourceAccessCheckerInterface;
use Dontdrinkandroot\Common\CrudOperation;
use Override;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @template T of object
 * @implements ProviderInterface<T>
 */
class AccessCheckerProvider implements ProviderInterface
{
    /**
     * @param ProviderInterface<T> $decorated
     */
    public function __construct(
        private readonly ProviderInterface $decorated,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly ?string $event = null
    ) {
    }

    #[Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $security = $operation->getSecurity();
        $securityPostDenormalize = $operation->getSecurityPostDenormalize();
        $securityPostValidation = $operation->getSecurityPostValidation();

        /* Abort early if custom security is defined */
        if (null !== $security || null !== $securityPostDenormalize || null !== $securityPostValidation) {
            return $this->decorated->provide($operation, $uriVariables, $context);
        }

        $crudOperation = match (get_class($operation)) {
            GetCollection::class => CrudOperation::LIST,
            Post::class => CrudOperation::CREATE,
            Get::class => CrudOperation::READ,
            Put::class => CrudOperation::UPDATE,
            default => null,
        };

        if (null === $this->event) {
            if (CrudOperation::LIST === $crudOperation) {
                if (!$this->authorizationChecker->isGranted(CrudOperation::LIST->value, $operation->getClass())) {
                    throw new AccessDeniedException();
                }
            }

            if (CrudOperation::CREATE === $crudOperation) {
                if (!$this->authorizationChecker->isGranted(CrudOperation::CREATE->value, $operation->getClass())) {
                    throw new AccessDeniedException();
                }
            }
        }

        $data = $this->decorated->provide($operation, $uriVariables, $context);

        $crudOperation = match (get_class($operation)) {
            GetCollection::class => CrudOperation::LIST,
            Post::class => CrudOperation::CREATE,
            Get::class => CrudOperation::READ,
            Put::class => CrudOperation::UPDATE,
            default => null,
        };

        return $data;
    }
}
