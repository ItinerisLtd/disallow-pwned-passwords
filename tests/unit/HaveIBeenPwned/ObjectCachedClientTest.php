<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Test\HaveIBeenPwned;

use Codeception\Test\Unit;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Client;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ClientInterface;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ObjectCachedClient;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Password;
use Mockery;
use WP_Mock;

class ObjectCachedClientTest extends Unit
{
    /**
     * @var \Itineris\DisallowPwnedPasswords\Test\UnitTester
     */
    protected $tester;

    public function testImplementsClientInterface()
    {
        $client = new ObjectCachedClient(
            new Client()
        );

        $this->assertInstanceOf(ClientInterface::class, $client);
    }

    public function testGetPwnedTimesCacheHit()
    {
        $result = [
            'ABC' => 123,
            'XYZ' => 456,
            '6622A23D08DAFACC8A11115A3CFC148E51D' => 279,
        ];

        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_cache_get')
               ->with('486B5', 'i_dpp_hibp')
               ->andReturn($result)
               ->once();

        $client = new ObjectCachedClient(
            new Client()
        );
        $password = new Password('password&');

        $actual = $client->getPwnedTimes($password);

        $this->assertSame(279, $actual);
    }

    public function testGetPwnedTimesCacheMiss()
    {
        $pwned = [
            'ABC' => 123,
            'XYZ' => 456,
            '6622A23D08DAFACC8A11115A3CFC148E51D' => 279,
        ];

        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_cache_get')
               ->with('486B5', 'i_dpp_hibp')
               ->andReturnFalse()
               ->once();

        $password = new Password('password&');

        $originalClient = Mockery::mock(Client::class);
        $originalClient->expects('fetchAndDecode')
                       ->with($password)
                       ->andReturn($pwned)
                       ->once();

        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_cache_set')
               ->with('486B5', $pwned, 'i_dpp_hibp', 604800)
               ->once();

        $client = new ObjectCachedClient($originalClient);

        $actual = $client->getPwnedTimes($password);

        $this->assertSame(279, $actual);
    }

    public function testGetPwnedTimesCacheMissAndApiUnreachable()
    {
        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\HaveIBeenPwned\wp_cache_get')
               ->with('486B5', 'i_dpp_hibp')
               ->andReturnFalse()
               ->once();

        $password = new Password('password&');

        $originalClient = Mockery::mock(Client::class);
        $originalClient->expects('fetchAndDecode')
                       ->with($password)
                       ->andReturn([])
                       ->once();

        $client = new ObjectCachedClient($originalClient);

        $actual = $client->getPwnedTimes($password);

        $this->assertSame(-1, $actual);
    }
}
