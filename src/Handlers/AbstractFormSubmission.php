<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Handlers;

use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ClientInterface;
use Itineris\DisallowPwnedPasswords\Plugin;
use Itineris\DisallowPwnedPasswords\PredicateInterface;
use Itineris\DisallowPwnedPasswords\TranslatorInterface;

abstract class AbstractFormSubmission
{
    protected const ERROR_CODE = Plugin::PREFIX . '_pwned';

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
}
