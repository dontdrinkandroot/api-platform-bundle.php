<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use Dontdrinkandroot\Common\CrudOperation;
use Override;
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

        /* Check if operation is allowed without data */
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
            Delete::class => CrudOperation::DELETE,
            default => null,
        };

        if (null === $this->event) {
            if (CrudOperation::DELETE === $crudOperation) {
                if (!$this->authorizationChecker->isGranted(CrudOperation::DELETE->value, $data)) {
                    throw new AccessDeniedException();
                }
            }
        }

        /* Check if operation is allowed with data after denormalization */
        if ('post_validate' === $this->event) {
            if (CrudOperation::CREATE === $crudOperation) {
                if (!$this->authorizationChecker->isGranted(CrudOperation::CREATE->value, $data)) {
                    throw new AccessDeniedException();
                }
            }
            if (CrudOperation::UPDATE === $crudOperation) {
                if (!$this->authorizationChecker->isGranted(CrudOperation::UPDATE->value, $data)) {
                    throw new AccessDeniedException();
                }
            }
        }

        return $data;
    }
}
