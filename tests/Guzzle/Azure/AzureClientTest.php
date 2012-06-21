<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests;

use Guzzle\Azure\AzureClient;

class AzureClientTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testBuilderCreatesClient()
    {
        $client = AzureClient::factory(array('subscription_id' => '123abc', 'key_path' => 'path-to-key.pem'));

        $request = $client->createRequest();
        $this->assertEquals('https://management.core.windows.net/123abc/', $request->getUrl());
    }
}
