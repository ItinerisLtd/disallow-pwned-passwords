<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\HaveIBeenPwned;

class Client implements ClientInterface
{
    const ENDPOINT = 'https://api.pwnedpasswords.com/range/';

    /**
     * Get number of pwned time of a given password.
     *
     * @param Password $password The password to be checked.
     *
     * @return int Return -1 if Have I Been Pwned API endpoint is unreachable.
     */
    public function getPwnedTimes(Password $password): int
    {
        $pwned = $this->fetchAndDecode($password);

        if (empty($pwned)) {
            return -1;
        }

        return $pwned[$password->getHashSuffix()] ?? 0;
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
     * @return array
     */
    public function fetchAndDecode(Password $password): array
    {
        $responseBody = $this->fetch($password);
        if (empty($responseBody)) {
            return [];
        }

        return $this->decode($responseBody);
    }

    /**
     * Fetch information about a hash prefix via Have I Been Pwned API endpoint.
     *
     * @param Password $password The password to be checked.
     *
     * @return string Body of the response if successful. Otherwise, empty string.
     */
    protected function fetch(Password $password): string
    {
        $url = static::ENDPOINT . $password->getHashPrefix();
        $response = wp_remote_get($url);

        $responseCode = wp_remote_retrieve_response_code($response);
        if (200 !== $responseCode) {
            return '';
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
            $exploded = explode(':', trim($suffixWithPwnedTimes));
            $suffix = (string) $exploded[0];
            $pwnedTimes = (int) $exploded[1];

            $pwned[$suffix] = $pwnedTimes;
        }

        return $pwned;
    }
}
