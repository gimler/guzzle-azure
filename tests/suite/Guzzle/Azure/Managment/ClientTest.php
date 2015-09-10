<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Managment;

use Guzzle\Azure\Managment\Client;

use org\bovigo\vfs\vfsStream;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $client = Client::factory(array('subscription_id' => 'a6c26c09-324b-41ea-aecb-00807a60be8a', 'key_path' => vfsStream::url('root/cert.pem')));

        $httpClient = $client->getHttpClient();
        $this->assertEquals('https://management.core.windows.net/a6c26c09-324b-41ea-aecb-00807a60be8a/', $httpClient->getBaseUrl());
        $this->assertEquals('vfs://root/cert.pem', $httpClient->getDefaultOption('cert'));
        $this->assertArraySubset(['x-ms-version' => '2015-04-01'], $httpClient->getDefaultOption('headers'));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage certificate key file `vfs://root/not-exists.pem` does not exist
     */
    public function testKeyNotExists()
    {
        Client::factory(array('subscription_id' => 'a6c26c09-324b-41ea-aecb-00807a60be8a', 'key_path' => vfsStream::url('root/not-exists.pem')));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Could not read certificate key file `vfs://root/not-readable.pem`
     */
    public function testKeyPermissionCheck()
    {
        Client::factory(array('subscription_id' => 'a6c26c09-324b-41ea-aecb-00807a60be8a', 'key_path' => vfsStream::url('root/not-readable.pem')));
    }
}