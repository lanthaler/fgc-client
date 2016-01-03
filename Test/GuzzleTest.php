<?php

/*
 * (c) Markus Lanthaler <mail@markus-lanthaler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ML\FgcClient\Test;

use Http\Client\HttpClient;
use Http\Client\Tests\HttpClientTest;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Message\StreamFactory\GuzzleStreamFactory;
use ML\FgcClient\FgcHttpClient;

/**
 * Tests for {@link FgcHttpClient}
 */
class GuzzleTest extends HttpClientTest
{
    /**
     * @dataProvider requestProvider
     * @group        integration
     */
    public function testSendRequest($method, $uri, array $headers, $body)
    {
        parent::testSendRequest(
            $method,
            $uri,
            $headers,
            $body
        );
    }

    /**
     * @dataProvider requestWithOutcomeProvider
     * @group        integration
     */
    public function testSendRequestWithOutcome($uriAndOutcome, $protocolVersion, array $headers, $body)
    {
        parent::testSendRequestWithOutcome(
            $uriAndOutcome,
            $protocolVersion,
            $headers,
            $body
        );
    }

    /**
     * @return HttpClient
     */
    protected function createHttpAdapter()
    {
        return new FgcHttpClient(new GuzzleMessageFactory(), new GuzzleStreamFactory());
    }
}
