<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Handlers;

use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ClientInterface;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Password;
use Itineris\DisallowPwnedPasswords\Plugin;
use Itineris\DisallowPwnedPasswords\PredicateInterface;
use Itineris\DisallowPwnedPasswords\TranslatorInterface;
use WP_Error;

abstract class AbstractFormSubmission
{
    const ERROR_CODE = Plugin::PREFIX . '_pwned';

    /**
     * The Have I Been Pwned API client.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * The translator.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * The predicate.
     *
     * @var PredicateInterface
     */
    protected $predicate;

    /**
     * FormSubmission constructor.
     *
     * @param ClientInterface     $hibp       The Have I Been Pwned API client.
     * @param PredicateInterface  $predicate  The predicate.
     * @param TranslatorInterface $translator The translator.
     */
    public function __construct(ClientInterface $hibp, PredicateInterface $predicate, TranslatorInterface $translator)
    {
        $this->client = $hibp;
        $this->predicate = $predicate;
        $this->translator = $translator;
    }

    /**
     * Check whether the password is pwned. Add error if pwned.
     *
     * @param string   $cleartext The password in plain text.
     * @param WP_Error $error     The WP_Error object.
     *
     * @return WP_Error
     */
    protected function check(string $cleartext, WP_Error $error): WP_Error
    {
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
