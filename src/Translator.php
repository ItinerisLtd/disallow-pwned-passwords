<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords;

class Translator implements TranslatorInterface
{
    /**
     * Pwned error message.
     *
     * @param int $pwnedTimes Number of times the password has been pwned.
     *
     * @return string
     */
    public function pwned(int $pwnedTimes): string
    {
        // Translators: %1$s is https://haveibeenpwned.com/ %2$d is the number of times it has been pwned.
        $message = __('The password has been <a href="%1$s">pwned</a> %2$d times.', 'disallow-pwned-passwords');

        return sprintf(
            wp_kses(
                $message,
                ['a' => ['href' => []]]
            ),
            'https://haveibeenpwned.com/',
            $pwnedTimes
        );
    }
}
