<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Handlers;

use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Password;
use WP_Error;

class FormSubmission extends AbstractFormSubmission
{
    /**
     * Validate password from superglobals not pwned.
     *
     * @param WP_Error $error The WP_Error object.
     *
     * @return void
     */
    public function handle(WP_Error $error): void
    {
        $errorCode = $error->get_error_code();
        if (! empty($errorCode)) {
            return;
        }

        $password = $this->makePasswordFromSuperglobals();
        if (null === $password) {
            return;
        }

        $pwnedTimes = $this->client->getPwnedTimes($password);

        if ($this->predicate->shouldDisallow($pwnedTimes)) {
            $error->add(
                static::ERROR_CODE,
                $this->translator->pwned($pwnedTimes)
            );
        }
    }

    /**
     * Make password instance from superglobals.
     *
     * @return Password|null returns null if password not found.
     */
    protected function makePasswordFromSuperglobals(): ?Password
    {
        if (empty($_POST['pass1']) && empty($_POST['password_1'])) { // WPCS: CSRF ok.
            return null;
        }

        $cleartext = wp_unslash($_POST['pass1'] ?? $_POST['password_1']); // WPCS: CSRF ok.

        return new Password($cleartext);
    }
}
