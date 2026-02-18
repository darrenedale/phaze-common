<?php

declare(strict_types=1);

namespace PhazeTest\Common\Framework;

use Closure;
use LogicException;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;

use function uopz_get_return;
use function uopz_set_return;
use function uopz_unset_return;

abstract class TestCase extends PhpUnitTestCase
{
    /** @var array<string,mixed>  */
    private array $m_functionMocks = [];

    /** Subclasses that reimplement tearDown() must call the parent implementation. */
    public function tearDown(): void
    {
        foreach (array_keys($this->m_functionMocks) as $function) {
            $this->removeFunctionMock($function);
        }

        parent::tearDown();
    }

    /**
     * Replace a function with a mock.
     *
     * @param string $function The name of the function to replace.
     * @param mixed $return The return value or closure with which to replace the function.
     */
    public function mockFunction(string $function, mixed $return): void
    {
        if (array_key_exists($function, $this->m_functionMocks)) {
            $this->removeFunctionMock($function);
        }

        uopz_set_return($function, $return, $return instanceof Closure);
        $this->m_functionMocks[$function] = $return;
    }

    /**
     * Check whether a function is currently mocked.
     *
     * @param string $function The function to check.
     *
     * @return bool `true` if it's mocked, `false` if not.
     */
    public function isFunctionMocked(string $function): bool
    {
        return array_key_exists($function, $this->m_functionMocks);
    }

    /**
     * Remove a function mock.
     *
     * @param string $function The name of the mocked function to restore.
     *
     * @throws LogicException if the provided function is not mocked.
     */
    public function removeFunctionMock(string $function): void
    {
        if (!array_key_exists($function, $this->m_functionMocks)) {
            throw new LogicException("Attempt to remove mock for function '{$function}' that isn't mocked.");
        }

        if ($this->m_functionMocks[$function] !== uopz_get_return(strtolower($function))) {
            throw new LogicException("Mock for function '{$function}' has been removed externally.");
        }

        unset($this->m_functionMocks[$function]);
        uopz_unset_return($function);
    }

    /**
     * Signal that a test uses a mechanism other than PHPUnit assertions and expectations to verify its expectations.
     */
    final public static function markTestExternallyVerified(): void
    {
        self::assertTrue(true);
    }
}
