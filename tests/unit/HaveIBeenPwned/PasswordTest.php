<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Test\HaveIBeenPwned;

use Codeception\Test\Unit;
use Itineris\DisallowPwnedPasswords\HaveIBeenPwned\Password;

class PasswordTest extends Unit
{
    /**
     * @var \Itineris\DisallowPwnedPasswords\Test\UnitTester
     */
    protected $tester;

    public function testGetPrefix()
    {
        $password = new Password('password&');

        $actual = $password->getPrefix();

        $this->assertSame('486B5', $actual);
    }

    public function testGetSuffix()
    {
        $password = new Password('password&');

        $actual = $password->getSuffix();

        $this->assertSame('6622A23D08DAFACC8A11115A3CFC148E51D', $actual);
    }
}
