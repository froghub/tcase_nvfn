<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\PhoneNumber;
use App\Service\PhoneNumberCacheService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\Target;

/**
 * @implements ProcessorInterface<PhoneNumber, void>
 */
class PhoneNumberProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire('api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
        #[Autowire('api_platform.doctrine.orm.state.remove_processor')]
        private readonly ProcessorInterface $removeProcessor,
        private readonly PhoneNumberCacheService $cacheService
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $processor = $this->persistProcessor;
        if($operation instanceof DeleteOperationInterface) {
            $processor = $this->removeProcessor;
        }

        $result = $processor->process($data, $operation, $uriVariables, $context);
        if ($data instanceof PhoneNumber && $data->getId()) {
            $this->cacheService->invalidateCache($data->getId()->toString());
        }

        return $result;
    }
}
