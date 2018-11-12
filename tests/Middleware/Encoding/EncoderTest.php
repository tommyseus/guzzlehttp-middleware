<?php
/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SeusTest\GuzzleHttp\Middleware\Encoding;

use GuzzleHttp;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Seus\GuzzleHttp\Middleware\Encoding;

/**
 * @covers  \Seus\GuzzleHttp\Middleware\Encoding\Encoder<extended>
 */
class EncoderTest extends TestCase
{
    /**
     * @param string|StreamInterface $body
     * @param string                 $from
     * @param string                 $to
     * @param string                 $expected
     *
     * @dataProvider dataProviderForTestInvoke
     */
    public function testEncoder($body, string $from, string $to, string $expected): void
    {
        $encoder = new Encoding\Encoder();
        $actual = $encoder($body, $from, $to);

        $this->assertSame($expected, (string) $actual);
    }

    public function dataProviderForTestInvoke(): array
    {
        return [
            [
                \mb_convert_encoding('abcäöü', 'iso-8859-1', 'UTF-8'),
                'iso-8859-1',
                'UTF-8',
                'abcäöü',
            ],
            [
                GuzzleHttp\Psr7\stream_for(\mb_convert_encoding('abcäöü', 'iso-8859-1', 'UTF-8')),
                'iso-8859-1',
                'iso-8859-1',
                \mb_convert_encoding('abcäöü', 'iso-8859-1', 'UTF-8'),
            ],
            [
                'abcäöü',
                'UTF-8',
                'ISO-8859-1',
                \mb_convert_encoding('abcäöü', 'iso-8859-1', 'UTF-8'),
            ],
            [
                \mb_convert_encoding('€', 'iso-8859-15', 'UTF-8'),
                'iso-8859-15',
                'UTF-8',
                '€',
            ],
        ];
    }

    public function testInvokeWithException01(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value needs to be an string or implements the StreamInterface, '
            . 'got \'integer\' instead.');

        $encoder = new Encoding\Encoder();
        $encoder(123, 'UTF-8', 'UTF-8');
    }

    public function testInvokeWithException02(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Encoding \'bar\' is unknown.');

        $encoder = new Encoding\Encoder();
        $encoder('foo', 'bar', 'UTF-8');
    }

    public function testInvokeWithException03(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Encoding \'bar\' is unknown.');

        $encoder = new Encoding\Encoder();
        $encoder('foo', 'UTF-8', 'bar');
    }
}
