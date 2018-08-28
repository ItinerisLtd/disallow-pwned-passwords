<?php
declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords;

interface TranslatorInterface
{
    /**
     * Pwned error message.
     *
     * @param int $pwnedTimes Number of times the password has been pwned.
     *
     * @return string
     */
    public function pwned(int $pwnedTimes): string;
}
