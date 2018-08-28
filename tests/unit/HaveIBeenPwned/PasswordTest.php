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

    public function testGetHashPrefix()
    {
        $password = new Password('password&');

        $actual = $password->getHashPrefix();

        $this->assertSame('486B5', $actual);
    }

    public function testGetHashSuffix()
    {
        $password = new Password('password&');

        $actual = $password->getHashSuffix();

        $this->assertSame('6622A23D08DAFACC8A11115A3CFC148E51D', $actual);
    }

    public function testGetCleartext()
    {
        $password = new Password('password&');

        $actual = $password->getCleartext();

        $this->assertSame('password&', $actual);
    }
}
