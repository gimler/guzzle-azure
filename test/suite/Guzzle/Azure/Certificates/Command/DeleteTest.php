<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Certificates\Command;

/**
 * @author Gordon Franke <info@nevalon.de>
 */
class DeleteTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @covers Guzzle\Azure\Command\Delete
     */
    public function testDelete()
    {
        $client = $this->getServiceBuilder()->get('test.azure');
        $command = $client->getCommand('certificates.delete', array('thumbprint' => 'cert1'));
        $this->setMockResponse($client, 'Azure/Certificates/DeleteResponse');

        $client->execute($command);

        $this->assertEquals('https://management.core.windows.net/123abc/certificates/cert1', $command->getRequest()->getUrl());
        $this->assertEquals('DELETE', $command->getRequest()->getMethod());

        $result = $command->getResult();
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * @covers Guzzle\Azure\Command\Delete
     * @expectedException Guzzle\Http\Exception\ClientErrorResponseException
     */
    public function testDeleteNotFound()
    {
        $client = $this->getServiceBuilder()->get('test.azure');
        $command = $client->getCommand('certificates.delete', array('thumbprint' => 'cert1'));
        $this->setMockResponse($client, 'Azure/Certificates/DeleteNotFoundResponse');

        $client->execute($command);

        $this->assertEquals('https://management.core.windows.net/123abc/certificates/cert1', $command->getRequest()->getUrl());
        $this->assertEquals('DELETE', $command->getRequest()->getMethod());

        $command->getResult();
    }
}
