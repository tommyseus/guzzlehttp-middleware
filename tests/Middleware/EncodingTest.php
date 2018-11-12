<?php
/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SeusTest\GuzzleHttp\Middleware;

use GuzzleHttp;
use PHPUnit\Framework\TestCase;
use Seus\GuzzleHttp\Middleware\Encoding;

/**
 * @covers  \Seus\GuzzleHttp\Middleware\Encoding<extended>
 */
class EncodingTest extends TestCase
{
    /**
     * @param string $contentType
     * @param string $body
     * @param string $encodeTo
     * @param string $expectedContentType
     * @param string $expectedBody
     *
     * @dataProvider dataProviderForTestInvoke
     */
    public function testInvoke(
        string $contentType,
        string $body,
        string $encodeTo,
        string $expectedContentType,
        string $expectedBody
    ): void {
        $response = new GuzzleHttp\Psr7\Response(
            200,
            [
                'Content-Type' => $contentType,
            ],
            GuzzleHttp\Psr7\stream_for($body)
        );

        $middleware = new Encoding($encodeTo);
        $this->assertInternalType('callable', $middleware);

        $actualResponse = $middleware($response);

        $this->assertSame($expectedBody, (string) $actualResponse->getBody());
        $this->assertSame([$expectedContentType], $actualResponse->getHeader('Content-Type'));
    }

    public function dataProviderForTestInvoke(): array
    {
        return [
            [
                'text/json; charset=iso-8859-1',
                \mb_convert_encoding('abcäöü', 'iso-8859-1', 'UTF-8'),
                'UTF-8',
                'text/json; charset=UTF-8',
                'abcäöü',
            ],
            [
                'application/octet-stream; charset=iso-8859-1',
                \mb_convert_encoding('abcäöü', 'iso-8859-1', 'UTF-8'),
                'UTF-8',
                'application/octet-stream; charset=iso-8859-1',
                \mb_convert_encoding('abcäöü', 'iso-8859-1', 'UTF-8'),
            ],
            [
                'text/json; charset=iso-8859-1',
                \mb_convert_encoding('abcäöü', 'iso-8859-1', 'UTF-8'),
                'iso-8859-1',
                'text/json; charset=iso-8859-1',
                \mb_convert_encoding('abcäöü', 'iso-8859-1', 'UTF-8'),
            ],
            [
                'text/json; charset=UTF-8',
                'abcäöü',
                'ISO-8859-1',
                'text/json; charset=ISO-8859-1',
                \mb_convert_encoding('abcäöü', 'iso-8859-1', 'UTF-8'),
            ],
            [
                'text/json; charset=iso-8859-15',
                \mb_convert_encoding('€', 'iso-8859-15', 'UTF-8'),
                'UTF-8',
                'text/json; charset=UTF-8',
                '€',
            ],
        ];
    }
}
