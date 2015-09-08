<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Storage\Command;

use Guzzle\Azure\Tests\Storage\TestCase;

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
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Storage/ContainerListResponse');
        self::$mock->addResponse($response);

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('blob.container.list');

        $result = self::$client->execute($command);
        $this->assertCount(1, $result["containers"]);
        $this->assertEquals('test', $result["containers"][0]["Name"]);

        $lastRequest = self::$history->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals('/', $lastRequest->getPath());
    }
}