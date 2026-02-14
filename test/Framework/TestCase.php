<?php

declare(strict_types=1);

namespace PhazeTest\Common\Framework;

use PHPUnit\Framework\TestCase as PhpUnitTestCase;

class TestCase extends PhpUnitTestCase
{
    /**
     * Signal that a test uses a mechanism other than PHPUnit assertions and expectations to verify its expectations.
     */
    public static function markTestExternallyVerified(): void
    {
        self::assertTrue(true);
    }
}
