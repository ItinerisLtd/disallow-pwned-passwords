<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Test;

use Codeception\Test\Unit;
use Itineris\DisallowPwnedPasswords\Translator;
use Itineris\DisallowPwnedPasswords\TranslatorInterface;
use Mockery;
use WP_Mock;

class TranslatorTest extends Unit
{
    /**
     * @var \Itineris\DisallowPwnedPasswords\Test\UnitTester
     */
    protected $tester;

    public function testImplementsTranslatorInterface()
    {
        $translator = new Translator();

        $this->assertInstanceOf(TranslatorInterface::class, $translator);
    }

    public function testPwned()
    {
        $translator = new Translator();

        $actual = $translator->pwned(999);

        $this->assertSame(
            'The password has been <a href="https://haveibeenpwned.com/">pwned</a> 999 times.',
            $actual
        );
    }

    protected function _before()
    {
        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\__')
               ->with(Mockery::type('string'), 'disallow-pwned-passwords')
               ->andReturnUsing(function ($arg) {
                   return $arg;
               })
               ->zeroOrMoreTimes();

        WP_Mock::userFunction('Itineris\DisallowPwnedPasswords\wp_kses')
               ->with(Mockery::type('string'), Mockery::type('array'))
               ->andReturnUsing(function ($arg) {
                   return $arg;
               })
               ->zeroOrMoreTimes();
    }
}
