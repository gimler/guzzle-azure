<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Storage;

use Guzzle\Azure\Storage\Client;

use org\bovigo\vfs\vfsStream;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $client = Client::factory(array('account_name' => 'guzzle-azure', 'account_key' => 'abc'));

        $httpClient = $client->getHttpClient();
        $this->assertEquals('https://guzzle-azure.blob.core.windows.net/', $httpClient->getBaseUrl());
        $this->assertArraySubset(['x-ms-version' => '2015-02-21'], $httpClient->getDefaultOption('headers'));
    }
}