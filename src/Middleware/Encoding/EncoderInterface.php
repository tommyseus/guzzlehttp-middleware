<?php
/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Seus\GuzzleHttp\Middleware\Encoding;

use Psr\Http\Message\StreamInterface;

interface EncoderInterface
{
    /**
     * @param string|StreamInterface $body
     * @param string                 $from
     * @param string                 $to
     *
     * @return StreamInterface
     */
    public function __invoke($body, string $from, string $to): StreamInterface;
}
