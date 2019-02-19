<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Handlers;

use WP_Error;

class WCCheckoutFormSubmission extends AbstractFormSubmission
{
    /**
     * Validate password not pwned.
     *
     * @param  array    $data  An array of checkout form posted data.
     * @param  WP_Error $error Validation errors.
     *
     * @return void
     */
    public function handle(array $data, WP_Error $error)
    {
        $errorCode = $error->get_error_code();
        $cleartext = $data['account_password'] ?? '';

        if (! empty($errorCode) || empty($cleartext)) {
            return;
        }

        $this->check($cleartext, $error);
    }
}
