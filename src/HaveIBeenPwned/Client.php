<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\HaveIBeenPwned;

class Client implements ClientInterface
{
    protected const ENDPOINT = 'https://api.pwnedpasswords.com/range/';

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
     * Fetch and decode information about a hash prefix via Have I Been Pwned API endpoint.
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
    public function fetchAndDecode(Password $password): ?array
    {
        $responseBody = $this->fetch($password);
        if (null === $responseBody) {
            return null;
        }

        return $this->decode($responseBody);
    }

    /**
     * Fetch information about a hash prefix via Have I Been Pwned API endpoint.
     *
     * @param Password $password The password to be checked.
     *
     * @return string|null Body of the response if successful. Otherwise, null.
     */
    protected function fetch(Password $password): ?string
    {
        $url = static::ENDPOINT . $password->getHashPrefix();
        $response = wp_remote_get($url);

        $responseCode = wp_remote_retrieve_response_code($response);
        if (200 !== $responseCode) {
            return null;
        }

        return (string) $response['body'];
    }

    /**
     * Decode Have I Been Pwned API response.
     *
     * Example return:
     *   [
     *     'hash-suffix-1' => 123,
     *     'hash-suffix-2' => 456,
     *   ]
     *
     * @param string $responseBody The Have I Been Pwned API response.
     *
     * @return array
     */
    protected function decode(string $responseBody): array
    {
        $suffixesWithPwnedTimes = explode("\n", $responseBody);
        $suffixesWithPwnedTimes = array_filter($suffixesWithPwnedTimes);

        $pwned = [];
        foreach ($suffixesWithPwnedTimes as $suffixWithPwnedTimes) {
            [
                0 => $suffix,
                1 => $pwnedTimes,
            ] = explode(':', trim($suffixWithPwnedTimes));

            $pwned[(string) $suffix] = (int) $pwnedTimes;
        }

        return $pwned;
    }
}
