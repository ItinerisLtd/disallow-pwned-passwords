<?php
declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\HaveIBeenPwned;

interface ClientInterface
{
    /**
     * Get number of pwned time of a given password.
     *
     * @param Password $password The password to be checked.
     *
     * @return int|null Return null if Have I Been Pwned API endpoint is unreachable.
     */
    public function getPwnedTimes(Password $password): ?int;
}
