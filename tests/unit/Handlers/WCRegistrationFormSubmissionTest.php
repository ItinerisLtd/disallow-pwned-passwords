<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Test\Handlers;

use Codeception\Test\Unit;
use Itineris\DisallowPwnedPasswords\Handlers\AbstractFormSubmission;
use Itineris\DisallowPwnedPasswords\Handlers\WCRegistrationFormSubmission;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Client;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ClientInterface;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Password;
use Itineris\DisallowPwnedPasswords\Predicate;
use Itineris\DisallowPwnedPasswords\Translator;
use Itineris\DisallowPwnedPasswords\TranslatorInterface;
use Mockery;
use WP_Mock;

class WCRegistrationFormSubmissionTest extends Unit
{
    /**
     * @var \Itineris\DisallowPwnedPasswords\Test\UnitTester
     */
    protected $tester;

    public function testExtendsAbstractFormSubmission()
    {
        $formSubmission = new WCRegistrationFormSubmission(
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

        $formSubmission = new WCRegistrationFormSubmission(
            $client,
            new Predicate(),
            new Translator()
        );

        $error = Mockery::mock('WP_Error');
        $error->expects('get_error_code')
              ->withNoArgs()
              ->andReturn('boom')
              ->once();

        $actual = $formSubmission->handle($error, '', 'password&');

        $this->assertSame($error, $actual);
    }

    public function testHandleNoPasswordFound()
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->expects('getPwnedTimes')
               ->never();

        $formSubmission = new WCRegistrationFormSubmission(
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

        $actual = $formSubmission->handle($error, '', '');

        $this->assertSame($error, $actual);
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

        $formSubmission = new WCRegistrationFormSubmission(
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

        $actual = $formSubmission->handle($error, '', 'password&');

        $this->assertSame($error, $actual);
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

        $formSubmission = new WCRegistrationFormSubmission(
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

        $actual = $formSubmission->handle($error, '', 'password&');

        $this->assertSame($error, $actual);
    }

    public function testHandleUnreachable()
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->expects('getPwnedTimes')
               ->with(Mockery::on(function (Password $password) {
                   return $password->getHashPrefix() === '486B5' &&
                          $password->getHashSuffix() === '6622A23D08DAFACC8A11115A3CFC148E51D';
               }))
               ->andReturnNull()
               ->once();

        $formSubmission = new WCRegistrationFormSubmission(
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

        $actual = $formSubmission->handle($error, '', 'password&');

        $this->assertSame($error, $actual);
    }

    protected function _before()
    {
        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\Handlers\wp_unslash')
               ->with(Mockery::type('string'))
               ->andReturnUsing(function ($arg) {
                   return $arg;
               })
               ->zeroOrMoreTimes();
    }
}
