<?php

declare(strict_types=1);

namespace PhazeTest\Common\Authorisation;

use DateTimeInterface;
use LogicException;
use Phaze\Common\Authorisation\AbstractAccessToken;
use PhazeTest\Common\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AbstractAccessToken::class)]
class AbstractAccessTokenTest extends TestCase
{
    /** Helper to create an AbstractAccessToken instance to test with. */
    private static function createTestToken(int $notBefore, int $expiresOn): AbstractAccessToken
    {
        return new class($notBefore, $expiresOn) extends AbstractAccessToken {
            private int $m_notBefore;

            private int $m_expiresOn;

            public function __construct(int $notBefore, int $expiresOn)
            {
                $this->m_notBefore = $notBefore;
                $this->m_expiresOn = $expiresOn;
            }

            public function token(): string
            {
                throw new LogicException("token() should not be called in this test");
            }

            public function type(): string
            {
                throw new LogicException("type() should not be called in this test");
            }

            public function notBefore(): int
            {
                return $this->m_notBefore;
            }

            public function notBeforeDateTime(): DateTimeInterface
            {
                throw new LogicException("notBeforeDateTime() should not be called in this test");
            }

            public function expiresOn(): int
            {
                return $this->m_expiresOn;
            }

            public function expiresOnDateTime(): DateTimeInterface
            {
                throw new LogicException("expiresOnDateTime() should not be called in this test");
            }

            public function resource(): string
            {
                throw new LogicException("resource() should not be called in this test");
            }

            public function headers(): array
            {
                throw new LogicException("headers() should not be called in this test");
            }
        };
    }

    /** Ensure hasExpired() correctly reports an expired token when the time is before its notBefore timestamp. */
    public function testHasExpired1(): void
    {
        // 2026-02-18T18:02:39.000Z
        $this->mockFunction("time", 1771437759);
        $token = self::createTestToken(1771437760, 1771437761);
        self::assertTrue($token->hasExpired());
    }

    /** Ensure hasExpired() correctly reports an expired token when the time is on its expiresOn timestamp. */
    public function testHasExpired2(): void
    {
        // 2026-02-18T18:02:39.000Z
        $this->mockFunction("time", 1771437759);
        $token = self::createTestToken(1771437758, 1771437759);
        self::assertTrue($token->hasExpired());
    }

    /** Ensure hasExpired() correctly reports an expired token when the time is after its expiresOn timestamp. */
    public function testHasExpired3(): void
    {
        // 2026-02-18T18:02:40.000Z
        $this->mockFunction("time", 1771437760);
        $token = self::createTestToken(1771437758, 1771437759);
        self::assertTrue($token->hasExpired());
    }

    /** Ensure hasExpired() correctly reports a valid token when the time is on its notBefore timestamp. */
    public function testHasExpired4(): void
    {
        // 2026-02-18T18:02:39.000Z
        $this->mockFunction("time", 1771437759);
        $token = self::createTestToken(1771437759, 1771437760);
        self::assertFalse($token->hasExpired());
    }

    /** Ensure hasExpired() correctly reports a valid token when the time is after its notBefore timestamp. */
    public function testHasExpired5(): void
    {
        // 2026-02-18T18:02:40.000Z
        $this->mockFunction("time", 1771437760);
        $token = self::createTestToken(1771437759, 1771437761);
        self::assertFalse($token->hasExpired());
    }

    /** Ensure hasExpired() correctly reports a valid token when the time is before its expiresOn timestamp. */
    public function testHasExpired6(): void
    {
        // 2026-02-18T18:02:40.000Z
        $this->mockFunction("time", 1771437760);
        $token = self::createTestToken(1771437759, 1771437761);
        self::assertFalse($token->hasExpired());
    }
}
