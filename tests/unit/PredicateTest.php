<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\Test;

use Codeception\Test\Unit;
use Itineris\DisallowPwnedPasswords\Predicate;
use Itineris\DisallowPwnedPasswords\PredicateInterface;

class PredicateTest extends Unit
{
    /**
     * @var \Itineris\DisallowPwnedPasswords\Test\UnitTester
     */
    protected $tester;

    public function testImplementsPredicateInterface()
    {
        $predicate = new Predicate();

        $this->assertInstanceOf(PredicateInterface::class, $predicate);
    }

    public function testShouldDisallow()
    {
        $predicate = new Predicate();

        $actual = $predicate->shouldDisallow(999);

        $this->assertTrue($actual);
    }

    public function testShouldAllowZero()
    {
        $predicate = new Predicate();

        $actual = $predicate->shouldDisallow(0);

        $this->assertFalse($actual);
    }

    public function testShouldAllowMinusOne()
    {
        $predicate = new Predicate();

        $actual = $predicate->shouldDisallow(-1);

        $this->assertFalse($actual);
    }
}
