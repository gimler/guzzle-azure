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
class DeleteTest extends TestCase
{
    /**
     * This covers blob.container.delete
     */
    public function testDelete()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Storage/ContainerDeleteResponse');
        self::$mock->addResponse($response);

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('blob.container.delete', array('container' => 'test'));

        $result = self::$client->execute($command);

        $lastRequest = self::$history->getLastRequest();
        $this->assertEquals('DELETE', $lastRequest->getMethod());
        $this->assertEquals('/test', $lastRequest->getPath());

        $lastResponse = self::$history->getLastResponse();
        $this->assertEquals(202, $lastResponse->getStatusCode());
        $this->assertEquals('ab7ba892-0001-0025-589b-e99921000000', $lastResponse->getHeader('x-ms-request-id'));
    }

    /**
     * This covers blob.container.delete
     */
    public function testDeleteNotFound()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Storage/ContainerDeleteNotFoundResponse');
        self::$mock->addResponse($response);

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('blob.container.delete', array('container' => 'test'));

        try {
            $result = self::$client->execute($command);
        } catch (CommandClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(404, $response->getStatusCode());

            $xml = $response->xml();
            $this->assertEquals($xml->Code, "ContainerNotFound");
            $this->assertEquals($xml->Message, "The specified container does not exist.\nRequestId:a435e04f-0001-0036-4339-eaacc0000000\nTime:2015-09-08T13:25:42.9995218Z");
        }
    }
}
