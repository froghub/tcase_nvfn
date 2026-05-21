<?php

namespace App\Service;

use App\Entity\PhoneNumber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PhoneNumberCacheService
{
    public function __construct(
        private readonly AdapterInterface $cache,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function getCachedNumber(string $id): ?PhoneNumber
    {
        return $this->cache->get($id, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(600);
            return $this->entityManager->getRepository(PhoneNumber::class)->find($id);
        });
    }

    public function invalidateCache(string $id): void
    {
        $this->cache->delete($id);
    }
}
