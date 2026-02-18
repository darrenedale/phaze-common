<?php

declare(strict_types=1);

namespace PhazeTest\Common\Authorisation;

use Phaze\Common\Authorisation\AccessToken;
use Phaze\Common\Exceptions\PhazeException;
use PhazeTest\Common\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(AccessToken::class)]
class AccessTokenTest extends TestCase
{
    private const TestTokenType = "test-token-type";

    private const TestResource = "test-resource";

    private const TestToken = "7080770e-af90-4a90-9f24-500c8401238a";

    // 2026-02-18T18:02:40.000Z
    private const TestNotBefore = 1771437760;

    // 2026-02-18T18:24:40.000Z
    private const TestExpiresOn = 1771439080;

    /** Valid 2026-02-18T18:02:40.000Z to 2026-02-18T18:24:40.000Z */
    private const TestTokenJson = <<<JSON
        {
            "token_type": "test-token-type",
            "resource": "test-resource",
            "access_token": "7080770e-af90-4a90-9f24-500c8401238a",
            "not_before": 1771437760,
            "expires_on": 1771439080
        }
        JSON;

    private AccessToken $m_accessToken;

    /** Provides JSON with incorrect data types for the expected properties. */
    public static function providerJsonWithIncorrectPropertyTypes(): iterable
    {
        yield "token-type" => [<<<JSON
            {
                "token_type": ["test-token-type"],
                "resource": "test-resource",
                "access_token": "7080770e-af90-4a90-9f24-500c8401238a",
                "not_before": 1771437760,
                "expires_on": 1771439080
            }
            JSON];

        yield "resource" => [<<<JSON
            {
                "token_type": "test-token-type",
                "resource": ["test-resource"],
                "access_token": "7080770e-af90-4a90-9f24-500c8401238a",
                "not_before": 1771437760,
                "expires_on": 1771439080
            }
            JSON];

        yield "access-token" => [<<<JSON
            {
                "token_type": "test-token-type",
                "resource": "test-resource",
                "access_token": ["7080770e-af90-4a90-9f24-500c8401238a"],
                "not_before": 1771437760,
                "expires_on": 1771439080
            }
            JSON];

        yield "not-before" => [<<<JSON
            {
                "token_type": "test-token-type",
                "resource": "test-resource",
                "access_token": "7080770e-af90-4a90-9f24-500c8401238a",
                "not_before": "invalid",
                "expires_on": 1771439080
            }
            JSON];

        yield "expires-on" => [<<<JSON
            {
                "token_type": "test-token-type",
                "resource": "test-resource",
                "access_token": "7080770e-af90-4a90-9f24-500c8401238a",
                "not_before": 1771437760,
                "expires_on": "invalid"
            }
            JSON];
    }

    /** Provides JSON with missing properties. */
    public static function providerJsonWithMissingProperties(): iterable
    {
        yield "token-type" => [<<<JSON
            {
                "resource": "test-resource",
                "access_token": "7080770e-af90-4a90-9f24-500c8401238a",
                "not_before": 1771437760,
                "expires_on": 1771439080
            }
            JSON];

        yield "resource" => [<<<JSON
            {
                "token_type": "test-token-type",
                "access_token": "7080770e-af90-4a90-9f24-500c8401238a",
                "not_before": 1771437760,
                "expires_on": 1771439080
            }
            JSON];

        yield "access-token" => [<<<JSON
            {
                "token_type": "test-token-type",
                "resource": "test-resource",
                "not_before": 1771437760,
                "expires_on": 1771439080
            }
            JSON];

        yield "not-before" => [<<<JSON
            {
                "token_type": "test-token-type",
                "resource": "test-resource",
                "access_token": "7080770e-af90-4a90-9f24-500c8401238a",
                "expires_on": 1771439080
            }
            JSON];

        yield "expires-on" => [<<<JSON
            {
                "token_type": "test-token-type",
                "resource": "test-resource",
                "access_token": "7080770e-af90-4a90-9f24-500c8401238a",
                "not_before": 1771437760
            }
            JSON];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->m_accessToken = AccessToken::fromJson(self::TestTokenJson);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->m_accessToken);
    }

    /** Ensure the token is reported correctly. */
    public function testToken1(): void
    {
        self::assertSame(self::TestToken, $this->m_accessToken->token());
    }

    /** Ensure the type is reported correctly. */
    public function testType1(): void
    {
        self::assertSame(self::TestTokenType, $this->m_accessToken->type());
    }

    /** Ensure the resource is reported correctly. */
    public function testResource1(): void
    {
        self::assertSame(self::TestResource, $this->m_accessToken->resource());
    }

    /** Ensure the lower bound of the token validity period is reported correctly. */
    public function testNotBefore1(): void
    {
        self::assertSame(self::TestNotBefore, $this->m_accessToken->notBefore());
    }

    /** Ensure the token correctly reports the formatted lower bound of its validity period. */
    public function testNotBeforeDateTime1(): void
    {
        self::assertSame(
            "2026-02-18 18:02:40 +0000",
            $this->m_accessToken->notBeforeDateTime()->format("Y-m-d H:i:s O"),
        );
    }

    /** Ensure the upper bound of the token validity period is reported correctly. */
    public function testExpiresOn1(): void
    {
        self::assertSame(self::TestExpiresOn, $this->m_accessToken->expiresOn());
    }

    /** Ensure the token correctly reports the formatted lower bound of its validity period. */
    public function testExpiresOnDateTime1(): void
    {
        self::assertSame(
            "2026-02-18 18:24:40 +0000",
            $this->m_accessToken->expiresOnDateTime()->format("Y-m-d H:i:s O"),
        );
    }

    /** Ensure the token can be cast to string. */
    public function testToString1(): void
    {
        self::assertSame(self::TestToken, (string) $this->m_accessToken);
    }

    /** Ensure the token provides the correct headers. */
    public function testHeaders1(): void
    {
        self::assertSame(["Authorization" => "Bearer " . self::TestToken], $this->m_accessToken->headers());
    }

    /** Ensure hasExpired() correctly reports an expired token when the time is before its notBefore timestamp. */
    public function testHasExpired1(): void
    {
        // 2026-02-18T18:02:39.000Z
        $this->mockFunction("time", 1771437759);
        self::assertTrue($this->m_accessToken->hasExpired());
    }

    /** Ensure hasExpired() correctly reports an expired token when the time is on its expiresOn timestamp. */
    public function testHasExpired2(): void
    {
        // 2026-02-18T18:24:40.000Z
        $this->mockFunction("time", 1771439080);
        self::assertTrue($this->m_accessToken->hasExpired());
    }

    /** Ensure hasExpired() correctly reports an expired token when the time is after its expiresOn timestamp. */
    public function testHasExpired3(): void
    {
        // 2026-02-18T18:24:41.000Z
        $this->mockFunction("time", 1771439081);
        self::assertTrue($this->m_accessToken->hasExpired());
    }

    /** Ensure hasExpired() correctly reports a valid token when the time is on its notBefore timestamp. */
    public function testHasExpired4(): void
    {
        // 2026-02-18T18:02:40.000Z
        $this->mockFunction("time", 1771437760);
        self::assertFalse($this->m_accessToken->hasExpired());
    }

    /** Ensure hasExpired() correctly reports a valid token when the time is after its notBefore timestamp. */
    public function testHasExpired5(): void
    {
        // 2026-02-18T18:02:41.000Z
        $this->mockFunction("time", 1771437761);
        self::assertFalse($this->m_accessToken->hasExpired());
    }

    /** Ensure hasExpired() correctly reports a valid token when the time is before its expiresOn timestamp. */
    public function testHasExpired6(): void
    {
        // 2026-02-18T18:24:39.000Z
        $this->mockFunction("time", 1771439079);
        self::assertFalse($this->m_accessToken->hasExpired());
    }

    /** Ensure fromJson() throws the expected exception when parsing invalid JSON. */
    public function testFromJson1(): void
    {
        $this->expectException(PhazeException::class);
        $this->expectExceptionMessage("Expected valid JSON Azure AccessToken data structure, found invalid JSON");
        AccessToken::fromJson("{");
    }

    /**
     * Ensure fromJson() throws the expected exception when the JSON contains an incorrect data type for a parsed
     * property.
     */
    #[DataProvider("providerJsonWithIncorrectPropertyTypes")]
    public function testFromJson2(string $json): void
    {
        $this->expectException(PhazeException::class);
        $this->expectExceptionMessage("Invalid data type found in Azure AccessToken data structure");
        AccessToken::fromJson($json);
    }

    /**
     * Ensure fromJson() throws the expected exception when the JSON is missing an expected property.
     */
    #[DataProvider("providerJsonWithMissingProperties")]
    public function testFromJson3(string $json): void
    {
        $this->expectException(PhazeException::class);
        $this->expectExceptionMessage("Expected JSON property missing from Azure AccessToken data structure");
        AccessToken::fromJson($json);
    }
}
