<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\PhoneNumber;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends ApiTestCase
{
    private Client $client;

    protected function setUp(): void
    {
        // Инициализируем клиент и сразу задаем дефолтные опции для всех запросов
        $this->client = static::createClient([], [
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);
    }
    public function testCreatePhoneNumberSuccess(): void
    {
        $response = $this->client->request('POST', '/api/phone_numbers', [
            'json' => [
                'number' => '79991112233',
                'tariff' => 'Tariff'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        $this->assertJsonContains([
            'number' => '79991112233',
            'tariff' => 'Tariff',
            'status' => 'active'
        ]);

        $responseData = $response->toArray();
        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('createdAt', $responseData);
    }

    public function testCreatePhoneNumberValidationError(): void
    {

        $this->client->request('POST', '/api/phone_numbers', [
            'json' => [
                'number' => '12345678901234567',
                'tariff' => 'Smart'
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
    }
}
