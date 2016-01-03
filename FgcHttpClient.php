<?php

/*
 * (c) Markus Lanthaler <mail@markus-lanthaler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ML\FgcClient;

use Http\Client\Exception\NetworkException;
use Http\Client\Exception\RequestException;
use Http\Message\MessageFactory;
use Http\Message\StreamFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A file_get_contents-based HTTP client.
 *
 * @author Markus Lanthaler <mail@markus-lanthaler.com>
 *
 * @link http://httplug.io/
 */
class FgcHttpClient implements \Http\Client\HttpClient
{
    /**
     * The message factory
     *
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * The stream factory
     *
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * The request timeout.
     *
     * @var int
     */
    private $timeout = 30;

    /**
     * Should redirects be followed?
     *
     * @var bool
     */
    private $followRedirects = false;

    /**
     * Constructor
     *
     * @param MessageFactory $messageFactory  HTTP Message factory
     * @param StreamFactory  $streamFactory   HTTP Stream factory
     * @param int            $timeout         Request timeout in seconds
     * @param bool           $followRedirects If set to true, redirects are followed automatically
     *
     * @throws \InvalidArgumentException If an invalid IRI is passed.
     *
     * @api
     */
    public function __construct(
        MessageFactory $messageFactory,
        StreamFactory $streamFactory,
        $timeout = 30,
        $followRedirects = false
    ) {
        $this->messageFactory = $messageFactory;
        $this->streamFactory = $streamFactory;
        $this->timeout = $timeout;
        $this->followRedirects = $followRedirects;
    }

    /**
     * Sends a PSR-7 request.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Http\Client\Exception If an error happens during processing the request.
     * @throws \Exception             If processing the request is impossible (eg. bad configuration).
     */
    public function sendRequest(RequestInterface $request)
    {
        $body = (string) $request->getBody();

        $headers = [];
        foreach (array_keys($request->getHeaders()) as $headerName) {
            if (strtolower($headerName) === 'content-length') {
                $values = array(strlen($body));
            } else {
                $values = $request->getHeader($headerName);
            }
            foreach ($values as $value) {
                $headers[] = $headerName . ': ' . $value;
            }
        }

        $streamContextOptions = array(
            'protocol_version' => $request->getProtocolVersion(),
            'method'           => $request->getMethod(),
            'header'           => implode("\r\n", $headers),
            'timeout'          => $this->timeout,
            'ignore_errors'    => true,
            'follow_location'  => $this->followRedirects ? 1 : 0,
            'max_redirects'    => 100
        );

        if (strlen($body) > 0) {
            $streamContextOptions['content'] = $body;
        }

        $context = stream_context_create(array(
            'http' => $streamContextOptions,
            'https' => $streamContextOptions
        ));

        $httpHeadersOffset = 0;
        $finalUrl = (string) $request->getUri();

        stream_context_set_params(
            $context,
            array('notification' =>
                function (
                    $code,
                    $severity,
                    $msg,
                    $msgCode,
                    $bytesTx,
                    $bytesMax
                ) use (
                    &$remoteDocument,
                    &$http_response_header,
                    &$httpHeadersOffset
                ) {
                    if ($code === STREAM_NOTIFY_REDIRECTED) {
                        $finalUrl = $msg;
                        $httpHeadersOffset = count($http_response_header);
                    }
                }
            )
        );


        $response = $this->messageFactory->createResponse();
        if (false === ($responseBody = @file_get_contents((string) $request->getUri(), false, $context))) {
            if (!isset($http_response_header)) {
                throw new NetworkException(
                    'Unable to execute request',
                    $request
                );
            }
        } else {
            $response = $response->withBody($this->streamFactory->createStream($responseBody));
        }

        $parser = new HeaderParser();
        try {
            return $parser->parseArray(array_slice($http_response_header, $httpHeadersOffset), $response);
        } catch (\Exception $e) {
            throw new RequestException($e->getMessage(), $request, $e);
        }
    }
}
