<?php

namespace Itineris\DisallowPwnedPasswords\Test\Handlers;

use Itineris\DisallowPwnedPasswords\Handlers\AbstractFormSubmission;
use Itineris\DisallowPwnedPasswords\Handlers\FormSubmission;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Client;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\ClientInterface;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Password;
use Itineris\DisallowPwnedPasswords\Predicate;
use Itineris\DisallowPwnedPasswords\Translator;
use Itineris\DisallowPwnedPasswords\TranslatorInterface;
use Mockery;
use WP_Mock;

class FormSubmissionTest extends \Codeception\Test\Unit
{
    /**
     * @var \Itineris\DisallowPwnedPasswords\Test\UnitTester
     */
    protected $tester;

    public function testExtendsAbstractFormSubmission()
    {
        $formSubmission = new FormSubmission(
            new Client(),
            new Predicate(),
            new Translator()
        );

        $this->assertInstanceOf(AbstractFormSubmission::class, $formSubmission);
    }

    public function testHandleAlreadyHasError()
    {
        $_POST['pass1'] = 'password&';

        $client = Mockery::mock(ClientInterface::class);
        $client->expects('getPwnedTimes')
               ->never();

        $formSubmission = new FormSubmission(
            $client,
            new Predicate(),
            new Translator()
        );

        $error = Mockery::mock('WP_Error');
        $error->expects('get_error_code')
              ->withNoArgs()
              ->andReturn('boom')
              ->once();

        $formSubmission->handle($error);
    }

    public function testHandlePass1Pwned()
    {
        $_POST['pass1'] = 'password&';

        $this->testHandlePwned();
    }

    protected function testHandlePwned()
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

        $formSubmission = new FormSubmission(
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

        $formSubmission->handle($error);
    }

    public function testHandlePassword1Pwned()
    {
        $_POST['password_1'] = 'password&';

        $this->testHandlePwned();
    }

    public function testHandleNoPasswordFound()
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->expects('getPwnedTimes')
               ->never();

        $formSubmission = new FormSubmission(
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

        $formSubmission->handle($error);
    }

    public function testHandlePass1NotPwned()
    {
        $_POST['pass1'] = 'password&';

        $this->testHandleNotPwned();
    }

    protected function testHandleNotPwned()
    {
        $client = Mockery::mock(ClientInterface::class);
        $client->expects('getPwnedTimes')
               ->with(Mockery::on(function (Password $password) {
                   return $password->getHashPrefix() === '486B5' &&
                          $password->getHashSuffix() === '6622A23D08DAFACC8A11115A3CFC148E51D';
               }))
               ->andReturn(0)
               ->once();

        $formSubmission = new FormSubmission(
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

        $formSubmission->handle($error);
    }

    public function testHandlePassword1NotPwned()
    {
        $_POST['password_1'] = 'password&';

        $this->testHandleNotPwned();
    }

    public function testHandleUnreachable()
    {
        $_POST['pass1'] = 'password&';

        $client = Mockery::mock(ClientInterface::class);
        $client->expects('getPwnedTimes')
               ->with(Mockery::on(function (Password $password) {
                   return $password->getHashPrefix() === '486B5' &&
                          $password->getHashSuffix() === '6622A23D08DAFACC8A11115A3CFC148E51D';
               }))
               ->andReturnNull()
               ->once();

        $formSubmission = new FormSubmission(
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

        $formSubmission->handle($error);
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

    protected function _after()
    {
        unset($_POST['pass1']);
        unset($_POST['password_1']);
    }
}
