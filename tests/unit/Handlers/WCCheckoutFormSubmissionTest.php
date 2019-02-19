<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Test\Handlers;

use Codeception\Test\Unit;
use Itineris\DisallowPwnedPasswords\Handlers\AbstractFormSubmission;
use Itineris\DisallowPwnedPasswords\Handlers\WCCheckoutFormSubmission;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Client;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ClientInterface;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Password;
use Itineris\DisallowPwnedPasswords\Predicate;
use Itineris\DisallowPwnedPasswords\Translator;
use Itineris\DisallowPwnedPasswords\TranslatorInterface;
use Mockery;

class WCCheckoutFormSubmissionTest extends Unit
{
    /**
     * @var \Itineris\DisallowPwnedPasswords\Test\UnitTester
     */
    protected $tester;

    public function testExtendsAbstractFormSubmission()
    {
        $formSubmission = new WCCheckoutFormSubmission(
            new Client(),
            new Predicate(),
            new Translator()
        );

        $this->assertInstanceOf(AbstractFormSubmission::class, $formSubmission);
    }

    public function testHandleAlreadyHasError()
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->expects('getPwnedTimes')
               ->withAnyArgs()
               ->never();

        $formSubmission = new WCCheckoutFormSubmission(
            $client,
            new Predicate(),
            new Translator()
        );

        $error = Mockery::mock('WP_Error');
        $error->expects('get_error_code')
              ->withNoArgs()
              ->andReturn('boom')
              ->once();

        $data = [
            'account_password' => 'password&',
        ];

        $formSubmission->handle($data, $error);
    }

    public function testHandleNoPasswordFound()
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->expects('getPwnedTimes')
               ->never();

        $formSubmission = new WCCheckoutFormSubmission(
            $client,
            new Predicate(),
            new Translator()
        );

        $error = Mockery::mock('WP_Error');
        $error->expects('get_error_code')
              ->withNoArgs()
              ->andReturn('')
              ->once();
        $error->expects('add')
              ->never();

        $data = [
            'account_password' => '',
        ];

        $formSubmission->handle($data, $error);
    }

    public function testHandlePwned()
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->expects('getPwnedTimes')
               ->with(Mockery::on(function (Password $password) {
                   return $password->getHashPrefix() === '486B5' &&
                          $password->getHashSuffix() === '6622A23D08DAFACC8A11115A3CFC148E51D';
               }))
               ->andReturn(9999)
               ->once();

        $translator = Mockery::mock(TranslatorInterface::class);
        $translator->expects('pwned')
                   ->with(9999)
                   ->andReturn('fake error message')
                   ->once();

        $formSubmission = new WCCheckoutFormSubmission(
            $client,
            new Predicate(),
            $translator
        );

        $error = Mockery::mock('WP_Error');
        $error->expects('get_error_code')
              ->withNoArgs()
              ->andReturn('')
              ->once();
        $error->expects('add')
              ->with('i_dpp_pwned', 'fake error message')
              ->once();

        $data = [
            'account_password' => 'password&',
        ];

        $formSubmission->handle($data, $error);
    }

    public function testHandleNotPwned()
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->expects('getPwnedTimes')
               ->with(Mockery::on(function (Password $password) {
                   return $password->getHashPrefix() === '486B5' &&
                          $password->getHashSuffix() === '6622A23D08DAFACC8A11115A3CFC148E51D';
               }))
               ->andReturn(0)
               ->once();

        $formSubmission = new WCCheckoutFormSubmission(
            $client,
            new Predicate(),
            new Translator()
        );

        $error = Mockery::mock('WP_Error');
        $error->expects('get_error_code')
              ->withNoArgs()
              ->andReturn('')
              ->once();
        $error->expects('add')
              ->never();

        $data = [
            'account_password' => 'password&',
        ];

        $formSubmission->handle($data, $error);
    }

    public function testHandleUnreachable()
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->expects('getPwnedTimes')
               ->with(Mockery::on(function (Password $password) {
                   return $password->getHashPrefix() === '486B5' &&
                          $password->getHashSuffix() === '6622A23D08DAFACC8A11115A3CFC148E51D';
               }))
               ->andReturn(-1)
               ->once();

        $formSubmission = new WCCheckoutFormSubmission(
            $client,
            new Predicate(),
            new Translator()
        );

        $error = Mockery::mock('WP_Error');
        $error->expects('get_error_code')
              ->withNoArgs()
              ->andReturn('')
              ->once();
        $error->expects('add')
              ->never();

        $data = [
            'account_password' => 'password&',
        ];

        $formSubmission->handle($data, $error);
    }
}
