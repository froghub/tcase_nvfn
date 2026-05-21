<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\PhoneNumber;
use App\Enum\Status;
use App\Service\PhoneNumberCacheService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @implements ProcessorInterface<PhoneNumber, void>
 */
class PhoneNumberProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service : 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
        private readonly PhoneNumberCacheService $cacheService
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
       if ($data->getId()) {
            $previousData = $this->cacheService->get($data->getId()->toString());
            if ($previousData && $previousData->getStatus() === Status::ARCHIVED) {
                throw new BadRequestHttpException('Нельзя изменять объект в архивном статусе.');
            }
        }

        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        if ($data instanceof PhoneNumber && $data->getId()) {
            $this->cacheService->invalidate($data->getId()->toString());
        }

        return $result;
    }
}
