<?php

namespace App\Service;

use App\Entity\PhoneNumber;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PhoneNumberCacheService
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
    ) {}

    public function get(string $id): ?PhoneNumber
    {
        $item = $this->cache->getItem('phone_' . $id);

        if (!$item->isHit()) {
            return null;
        }

        $item->expiresAfter(600);
        $this->cache->save($item);

        return $item->get();
    }

    public function set(string $id, PhoneNumber $phoneNumber): void
    {
        $item = $this->cache->getItem('phone_' . $id);
        $item->set($phoneNumber);
        $item->expiresAfter(600);

        $this->cache->save($item);
    }


    public function invalidate(string $id): void
    {
        $this->cache->deleteItem('phone_' . $id);
    }
}
