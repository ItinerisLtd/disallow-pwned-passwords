<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords;

class Predicate implements PredicateInterface
{
    /**
     * Whether the password should be allowed according to the number of times it has been pwned.
     *
     * @param int $pwnedTimes Number of times the password has been pwned.
     *
     * @return bool
     */
    public function shouldDisallow(int $pwnedTimes): bool
    {
        return $pwnedTimes > 0;
    }
}
