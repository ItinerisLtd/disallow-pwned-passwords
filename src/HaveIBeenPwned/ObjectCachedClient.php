<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\HaveIBeenPwned;

use Itineris\DisallowPwnedPasswords\Plugin;

class ObjectCachedClient implements ClientInterface
{
    protected const CACHE_GROUP = Plugin::PREFIX . '_hibp'; // Some object cache backend needs short keys.
    protected const CACHE_TTL_IN_SECONDS = 604800; // 1 week.

    /**
     * The original API client.
     *
     * @var Client
     */
    protected $client;

    /**
     * ObjectCachedClient constructor.
     *
     * @param Client $client The original API client.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get number of pwned time of a given password.
     *
     * @param Password $password The password to be checked.
     *
     * @return int|null Return null if Have I Been Pwned API endpoint is unreachable.
     */
    public function getPwnedTimes(Password $password): ?int
    {
        $pwned = $this->fetchAndDecode($password);

        if (null === $pwned) {
            return null;
        }

        return $pwned[$password->getSuffix()] ?? 0;
    }

    /**
     * Fetch and decode information about a hash prefix:
     *  - via WP Object Cache API
     *  - via Have I Been Pwned API endpoint
     *
     * Example return:
     *   [
     *     'hash-suffix-1' => 123,
     *     'hash-suffix-2' => 456,
     *   ]
     *
     * @param Password $password The password to be checked.
     *
     * @return array|null
     */
    protected function fetchAndDecode(Password $password): ?array
    {
        $result = wp_cache_get(
            $password->getHashPrefix(),
            static::CACHE_GROUP
        );

        if (false !== $result) {
            return (array) $result;
        }

        $result = $this->client->fetchAndDecode($password);
        if (null !== $result) {
            wp_cache_set(
                $password->getHashPrefix(),
                $result,
                static::CACHE_GROUP,
                static::CACHE_TTL_IN_SECONDS
            );
        }

        return $result;
    }
}
