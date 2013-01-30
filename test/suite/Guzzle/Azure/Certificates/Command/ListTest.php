<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Certificates\Command;

/**
 * @author Gordon Franke <info@nevalon.de>
 */
class ListTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * This covers certificates.list
     */
    public function testList()
    {
        $client = $this->getServiceBuilder()->get('test.azure');
        $command = $client->getCommand('certificates.list');
        $this->setMockResponse($client, 'Azure/Certificates/ListResponse');

        $client->execute($command);

        $this->assertEquals('https://management.core.windows.net/123abc/certificates', $command->getRequest()->getUrl());
    }
}
