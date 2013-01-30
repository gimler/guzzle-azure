<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Locations\Command;

/**
 * @author Gordon Franke <info@nevalon.de>
 */
class ListTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * This covers locations.list
     */
    public function testList()
    {
        $client = $this->getServiceBuilder()->get('test.azure');
        $command = $client->getCommand('locations.list');
        $this->setMockResponse($client, 'Azure/Locations/ListResponse');

        $client->execute($command);

        $this->assertEquals('https://management.core.windows.net/123abc/locations', $command->getRequest()->getUrl());
    }
}
