<?php

/*
 * (c) Markus Lanthaler <mail@markus-lanthaler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ML\FgcClient;

use Psr\Http\Message\ResponseInterface;

/**
 * HTTP response headers parser
 *
 * More or less a shameless copy of {@link https://github.com/php-http/curl-client curl-client's}
 * {@code HeadersParser}.
 *
 * @author Markus Lanthaler <mail@markus-lanthaler.com>
 */
class HeaderParser
{
    /**
     * Parse headers and write them to response object.
     *
     * @param string[]          $headers  Response headers as array of header lines.
     * @param ResponseInterface $response Response to write headers to.
     *
     * @return ResponseInterface
     *
     * @throws \InvalidArgumentException For invalid status code arguments.
     * @throws \RuntimeException
     */
    public function parseArray(array $headers, ResponseInterface $response)
    {
        $statusLine = trim(array_shift($headers));
        $parts = explode(' ', $statusLine, 3);
        if (count($parts) < 2 || substr(strtolower($parts[0]), 0, 5) !== 'http/') {
            throw new \RuntimeException(
                sprintf('"%s" is not a valid HTTP status line', $statusLine)
            );
        }

        $reasonPhrase = count($parts) > 2 ? $parts[2] : '';
        $response = $response
            ->withStatus((int) $parts[1], $reasonPhrase)
            ->withProtocolVersion(substr($parts[0], 5));

        foreach ($headers as $headerLine) {
            $headerLine = trim($headerLine);
            if ('' === $headerLine) {
                continue;
            }

            $parts = explode(':', $headerLine, 2);
            if (count($parts) !== 2) {
                throw new \RuntimeException(
                    sprintf('"%s" is not a valid HTTP header line', $headerLine)
                );
            }
            $name = trim(urldecode($parts[0]));
            $value = trim(urldecode($parts[1]));
            if ($response->hasHeader($name)) {
                $response = $response->withAddedHeader($name, $value);
            } else {
                $response = $response->withHeader($name, $value);
            }
        }

        return $response;
    }
}
