<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Handlers;

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
        $cleartext = $this->getPasswordCleartextFromSuperglobals();

        if (! empty($errorCode) || null === $cleartext) {
            return;
        }

        $this->check($cleartext, $error);
    }

    /**
     * Make password instance from superglobals.
     *
     * @return string|null returns null if password not found.
     */
    protected function getPasswordCleartextFromSuperglobals(): ?string
    {
        if (empty($_POST['pass1']) && empty($_POST['password_1'])) { // WPCS: CSRF ok.
            return null;
        }

        return wp_unslash($_POST['pass1'] ?? $_POST['password_1']); // WPCS: CSRF ok.
    }
}
