<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Handlers;

use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Password;
use WP_Error;

class WCRegistrationFormSubmission extends AbstractFormSubmission
{
    /**
     * Validate password not pwned.
     *
     * @param WP_Error $error     The WP_Error object.
     * @param string   $_username Unused.
     * @param string   $cleartext The user submitted password in plan text. Empty if going to be auto-generated.
     *
     * @return WP_Error
     */
    public function handle(WP_Error $error, string $_username, string $cleartext): WP_Error
    {
        $errorCode = $error->get_error_code();
        $cleartext = wp_unslash($cleartext);

        if (! empty($errorCode) || empty($cleartext)) {
            return $error;
        }

        $password = new Password($cleartext);

        $pwnedTimes = $this->client->getPwnedTimes($password);

        if ($this->predicate->shouldDisallow($pwnedTimes)) {
            $error->add(
                static::ERROR_CODE,
                $this->translator->pwned($pwnedTimes)
            );
        }

        return $error;
    }
}
