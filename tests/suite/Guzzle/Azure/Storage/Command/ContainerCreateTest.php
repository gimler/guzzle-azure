<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Storage\Command;

use Guzzle\Azure\Tests\Storage\TestCase;

use GuzzleHttp\Command\Exception\CommandClientException;

/**
 * @author Gordon Franke <info@nevalon.de>
 */
class CreateTest extends TestCase
{
    /**
     * This covers blob.container.create
     */
    public function testCreate()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Storage/ContainerCreateResponse');
        self::$mock->addResponse($response);

        $command = self::$client->getCommand('blob.container.create', array(
            'container' => 'test'
        ));

        $result = self::$client->execute($command);

        $lastRequest = self::$history->getLastRequest();
        $this->assertEquals('PUT', $lastRequest->getMethod());
        $this->assertEquals('/test', $lastRequest->getPath());

        $lastResponse = self::$history->getLastResponse();
        $this->assertEquals(201, $lastResponse->getStatusCode());
        $this->assertEquals('7dc8b581-0001-002b-349b-e9752a000000', $lastResponse->getHeader('x-ms-request-id'));
    }

    /**
     * This covers blob.container.create
     */
    public function testCreateAllreadyExist()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Storage/ContainerCreateAllreadyExistResponse');
        self::$mock->addResponse($response);

        $command = self::$client->getCommand('blob.container.create', array(
            'container' => 'test',
        ));

        try {
            $result = self::$client->execute($command);
        } catch (CommandClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(409, $response->getStatusCode());

            $xml = $response->xml();
            $this->assertEquals($xml->Code, "ContainerAlreadyExists");
            $this->assertEquals($xml->Message, "The specified container already exists.\nRequestId:ab7ba876-0001-0025-3c9b-e99921000000\nTime:2015-09-07T18:29:39.1791200Z");
        }
    }
}