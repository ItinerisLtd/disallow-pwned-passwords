<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords;

use Itineris\DisallowPwnedPasswords\Handlers\FormSubmission;
use Itineris\DisallowPwnedPasswords\Handlers\WCCheckoutFormSubmission;
use Itineris\DisallowPwnedPasswords\Handlers\WCRegistrationFormSubmission;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Client;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ClientInterface;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ObjectCachedClient;
use League\Container\Container;
use League\Container\ReflectionContainer;
use TypistTech\WPContainedHook\Hooks\Action;
use TypistTech\WPContainedHook\Hooks\Filter;
use TypistTech\WPContainedHook\Loader;

class Plugin
{
    public const PREFIX = 'i_dpp';

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
        $this->container = new Container();
        $this->loader = new Loader($this->container);
    }

    /**
     * Begins execution of the plugin.
     *
     * - set up the container
     * - add actions and filters
     *
     * @return void
     */
    public function run(): void
    {
        $this->setUpContainer();
        $this->setUpLoader();

        add_action('plugins_loaded', function (): void {
            do_action(static::PREFIX . '_register', $this->container);
        }, PHP_INT_MAX - 1000);

        add_action('plugins_loaded', function (): void {
            do_action(static::PREFIX . '_boot', $this->container);
        }, PHP_INT_MIN + 1000);
    }

    /**
     * Set up container.
     *
     * @return void
     */
    protected function setUpContainer(): void
    {
        // Register the reflection container as a delegate to enable auto wiring.
        $this->container->delegate(
            new ReflectionContainer()
        );

        $this->container->add(TranslatorInterface::class, Translator::class);
        $this->container->add(PredicateInterface::class, Predicate::class);
        $this->container->add(ClientInterface::class, ObjectCachedClient::class)
                        ->addArgument(Client::class);
    }

    /**
     * Set up loader.
     *
     * @return void
     */
    protected function setUpLoader(): void
    {
        $this->loader->add(
            new Action('user_profile_update_errors', FormSubmission::class, 'handle'),
            // See: WC_Form_Handler::process_reset_password
            // Home / My account / Lost password.
            new Action('validate_password_reset', FormSubmission::class, 'handle'),
            // See: WC_Form_Handler::save_account_details.
            // Home / My account / Account details.
            new Action('woocommerce_save_account_details_errors', FormSubmission::class, 'handle'),
            // See: WC_Form_Handler::process_registration.
            // Home / My account.
            new Filter('woocommerce_process_registration_errors', WCRegistrationFormSubmission::class, 'handle', 10, 3),
            // See: WC_Checkout::validate_checkout
            // Home / Checkout.
            new Action('woocommerce_after_checkout_validation', WCCheckoutFormSubmission::class, 'handle', 10, 2)
        );
        $this->loader->run();
    }
}
