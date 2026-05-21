<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\PhoneNumber;
use App\Service\PhoneNumberCacheService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProviderInterface<PhoneNumber>
 */
class PhoneNumberProvider implements ProviderInterface
{

    public function __construct(
//        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
//        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly PhoneNumberCacheService $cacheService,
    ) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if($operation instanceof CollectionOperationInterface) {
            return $this->collectionProvider->provide($operation, $uriVariables, $context);
        }
        $id = $uriVariables['id'] ?? null;

        if (!$id) {
            return null;
        }

        return $this->cacheService->getCachedNumber($id);
    }
}
