<?php

namespace Dontdrinkandroot\ApiPlatformBundle\Security;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
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

        $crudOperation = match ($operation::class) {
            GetCollection::class => CrudOperation::LIST,
            Post::class => CrudOperation::CREATE,
            Get::class => CrudOperation::READ,
            Put::class, Patch::class => CrudOperation::UPDATE,
            Delete::class => CrudOperation::DELETE,
            default => null,
        };

        /* Check if operation is allowed without data */
        if (null === $this->event) {
            if (in_array($crudOperation, [CrudOperation::LIST, CrudOperation::CREATE], true)) {
                if (!$this->authorizationChecker->isGranted($crudOperation->value, $operation->getClass())) {
                    throw new AccessDeniedException();
                }
            }
        }

        $data = $this->decorated->provide($operation, $uriVariables, $context);

        if (null === $this->event) {
            if (in_array($crudOperation, [CrudOperation::DELETE, CrudOperation::READ], true)) {
                if (!$this->authorizationChecker->isGranted($crudOperation->value, $data)) {
                    throw new AccessDeniedException();
                }
            }
        } elseif (in_array($this->event, ['post_denormalize', 'post_validate'], true)) {
            if (in_array($crudOperation, [CrudOperation::CREATE, CrudOperation::UPDATE], true)) {
                if (!$this->authorizationChecker->isGranted($crudOperation->value, $data)) {
                    throw new AccessDeniedException();
                }
            }
        }

        return $data;
    }
}
