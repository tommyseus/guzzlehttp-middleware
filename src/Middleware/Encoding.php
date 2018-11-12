<?php
/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Seus\GuzzleHttp\Middleware;

use GuzzleHttp;
use Psr\Http\Message\ResponseInterface;
use Seus\GuzzleHttp\Middleware\Encoding\Encoder;
use Seus\GuzzleHttp\Middleware\Encoding\EncoderInterface;

class Encoding
{
    /**
     * @var callable|EncoderInterface
     */
    protected $encoder;

    /**
     * @var string
     */
    protected $toEncoding;

    /**
     * @param string                         $toEncoding
     * @param callable|null|EncoderInterface $encoder
     */
    public function __construct(string $toEncoding = 'UTF-8', callable $encoder = null)
    {
        $this->encoder = $encoder ?? new Encoder();
        $this->toEncoding = $toEncoding;
    }

    public function __invoke(ResponseInterface $response): ResponseInterface
    {
        [
            'contentType' => $contentType,
            'charset' => $charset,
            'contentTypeHeader' => $contentTypeHeader,
        ] = $this->getContentType($response);

        if (!$this->validCharset((string) $charset) || !$this->validContentType((string) $contentType)) {
            return $response;
        }

        return $response
            ->withHeader('Content-Type', $this->createContentTypeHeaderLine($contentTypeHeader))
            ->withBody(($this->encoder)($response->getBody(), $charset, $this->toEncoding));
    }

    protected function getContentType(ResponseInterface $response): array
    {
        $contentType = [];

        if ($response->hasHeader('Content-Type')) {
            $contentType = GuzzleHttp\Psr7\parse_header($response->getHeader('Content-Type'));
        }

        return [
            'contentType' => $contentType[0][0] ?? null,
            'charset' => $contentType[0]['charset'] ?? 'UTF-8',
            'contentTypeHeader' => $contentType,
        ];
    }

    protected function createContentTypeHeaderLine(array $contentType): array
    {
        $contentType[0]['charset'] = $this->toEncoding;

        return \array_map([$this, 'encodeHeader'], $contentType);
    }

    protected function encodeHeader(array $header): string
    {
        $headerLine = '';

        foreach ($header as $key => $value) {
            if (!empty($headerLine)) {
                $headerLine .= '; ';
            }

            if (!\is_string($key)) {
                $headerLine .= $value;
            } else {
                $headerLine .= $key . '=' . $value;
            }
        }

        return $headerLine;
    }

    protected function validContentType(string $contentType): bool
    {
        return \strpos($contentType, 'text/') === 0
            || \in_array(
                $contentType,
                [
                    'application/atom+xml',
                    'application/json',
                    'application/ld+json',
                    'application/rss+xml',
                    'application/vnd.geo+json',
                    'application/xml',
                    'application/javascript',
                    'application/manifest+json',
                    'application/x-web-app-manifest+json',
                ],
                true
            );
    }

    protected function validCharset(string $charset): bool
    {
        return !empty($charset) && \strtoupper($charset) !== \strtoupper($this->toEncoding);
    }
}
