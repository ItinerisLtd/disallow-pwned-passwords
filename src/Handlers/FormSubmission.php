<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Handlers;

use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ClientInterface;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Password;
use Itineris\DisallowPwnedPasswords\Plugin;
use WP_Error;

class FormSubmission
{
    /**
     * The Have I Been Pwned API client.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * FormSubmission constructor.
     *
     * @param ClientInterface $hibp The Have I Been Pwned API client.
     */
    public function __construct(ClientInterface $hibp)
    {
        $this->client = $hibp;
    }

    /**
     * Validate password in superglobals.
     *
     * TODO: Test me.
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

        if (empty($_POST['pass1']) && empty($_POST['password_1'])) { // WPCS: CSRF ok.
            return;
        }

        $password = new Password(
            wp_unslash($_POST['pass1'] ?? $_POST['password_1']) // WPCS: CSRF ok.
        );

        $pwnedTimes = $this->client->getPwnedTimes($password);

        if (! $this->shouldDisallow($pwnedTimes)) {
            return;
        }

        $error->add(
            Plugin::PREFIX . '_pwned',
            $this->getMessage($pwnedTimes)
        );
    }

    /**
     * Whether the password should be allowed according to the number of times it has been pwned.
     *
     * @param int|null $pwnedTimes Number of times the password has been pwned.
     *
     * @return bool
     */
    protected function shouldDisallow(?int $pwnedTimes): bool
    {
        return null !== $pwnedTimes && $pwnedTimes > 0;
    }

    /**
     * Error message.
     *
     * TODO: Extract into a class.
     *
     * @param int $pwnedTimes Number of times the password has been pwned.
     *
     * @return string
     */
    protected function getMessage(int $pwnedTimes): string
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
