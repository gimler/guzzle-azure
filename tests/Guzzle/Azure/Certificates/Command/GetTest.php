<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Certificates\Command;

/**
 * @author Gordon Franke <info@nevalon.de>
 */
class GetTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @covers Guzzle\Azure\Command\Get
     */
    public function testGet()
    {
        $client = $this->getServiceBuilder()->get('test.azure');
        $command = $client->getCommand('certificates.get', array('thumbprint' => 'cert1'));
        $this->setMockResponse($client, 'Azure/Certificates/GetResponse');

        $client->execute($command);

        $this->assertEquals('https://management.core.windows.net/123abc/certificates/cert1', $command->getRequest()->getUrl());
        $this->assertEquals('GET', $command->getRequest()->getMethod());
    }

    /**
     * @covers Guzzle\Azure\Command\Det
     * @expectedException Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function testGetNotFound()
    {
        $client = $this->getServiceBuilder()->get('test.azure');
        $command = $client->getCommand('certificates.get', array('thumbprint' => 'cert1'));
        $this->setMockResponse($client, 'Azure/Certificates/GetNotFoundResponse');

        $client->execute($command);

        $this->assertEquals('https://management.core.windows.net/123abc/certificates/cert1', $command->getRequest()->getUrl());

        $command->getResult();
    }
}
