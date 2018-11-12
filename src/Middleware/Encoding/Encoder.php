<?php
/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Seus\GuzzleHttp\Middleware\Encoding;

use GuzzleHttp;
use Psr\Http\Message\StreamInterface;

class Encoder implements EncoderInterface
{
    /**
     * @var array
     */
    protected $encodings;

    public function __construct()
    {
        $encodings = \array_unique((array) \array_merge(
            \mb_list_encodings(),
            ...\array_map('mb_encoding_aliases', \mb_list_encodings())
        ));
        $this->encodings = \array_combine(\array_map('strtoupper', $encodings), $encodings);
    }

    /**
     * @param string|StreamInterface $body
     * @param string                 $from
     * @param string                 $to
     *
     * @return StreamInterface
     */
    public function __invoke($body, string $from, string $to): StreamInterface
    {
        if (!\is_string($body) && !$body instanceof StreamInterface) {
            throw new \InvalidArgumentException(\sprintf(
                'Value needs to be an string or implements the StreamInterface, got \'%s\' instead.',
                \is_object($body) ? \get_class($body) : \gettype($body)
            ));
        }

        $from2 = $this->encodings[\strtoupper($from)] ?? null;
        if (!$from2) {
            throw new \InvalidArgumentException(\sprintf('Encoding \'%s\' is unknown.', $from));
        }

        $to2 = $this->encodings[\strtoupper($to)] ?? null;
        if (!$to2) {
            throw new \InvalidArgumentException(\sprintf('Encoding \'%s\' is unknown.', $to));
        }

        return GuzzleHttp\Psr7\stream_for(\mb_convert_encoding(
            (string) $body,
            $to2,
            $from2
        ));
    }
}
