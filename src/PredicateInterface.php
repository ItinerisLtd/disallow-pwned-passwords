<?php
declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords;

interface PredicateInterface
{
    /**
     * Whether the password should be allowed according to the number of times it has been pwned.
     *
     * @param int|null $pwnedTimes Number of times the password has been pwned.
     *
     * @return bool
     */
    public function shouldDisallow(?int $pwnedTimes): bool;
}
