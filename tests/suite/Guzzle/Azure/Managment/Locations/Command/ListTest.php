<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Managment\Locations\Command;

use Guzzle\Azure\Tests\Managment\TestCase;

/**
 * @author Gordon Franke <info@nevalon.de>
 */
class ListTest extends TestCase
{
    /**
     * This covers locations.list
     */
    public function testList()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Managment/Locations/ListResponse');
        self::$mock->addResponse($response);

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('locations.list');

        $result = self::$client->execute($command);
        $this->assertCount(7, $result["locations"]);

        $lastRequest = self::$history->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals('/a6c26c09-324b-41ea-aecb-00807a60be8a/locations', $lastRequest->getPath());
    }
}
