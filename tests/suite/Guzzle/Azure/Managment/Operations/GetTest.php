<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Operations\Command;

use Guzzle\Azure\Tests\Managment\TestCase;

use GuzzleHttp\Command\Exception\CommandClientException;

/**
 * @author Gordon Franke <info@nevalon.de>
 */
class GetTest extends TestCase
{
    /**
     * This covers certificates.get
     */
    public function testGet()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Managment/Operations/GetResponse');
        self::$mock->addResponse($response);

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('operations.get', array('request_id' => '9f2db6a1-4533-a849-8a68-2299c00ec3d5'));

        $result = self::$client->execute($command);
        $this->assertEquals('InProgress', $result['Status']);

        $lastRequest = self::$history->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals('/a6c26c09-324b-41ea-aecb-00807a60be8a/operations/9f2db6a1-4533-a849-8a68-2299c00ec3d5', $lastRequest->getPath());
    }

    /**
     * This covers certificates.get
     */
    public function testGetNotFound()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Managment/Operations/GetNotFoundResponse');
        self::$mock->addResponse($response);

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('operations.get', array('request_id' => '9f2db6a1-4533-a849-8a68-2299c00ec3d5'));

        try {
            $result = self::$client->execute($command);
        } catch (CommandClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());

            $xml = $response->xml();
            $this->assertEquals($xml->Code, "BadRequest");
            $this->assertEquals($xml->Message, "The operation request ID was not found.");
        }
    }
}