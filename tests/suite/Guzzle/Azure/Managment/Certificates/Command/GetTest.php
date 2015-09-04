<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Certificates\Command;

use Guzzle\Azure\Tests\TestCase;

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
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Certificates/GetResponse');
        self::$mock->addResponse($response);

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('certificates.get', array('thumbprint' => 'cert1'));

        $result = self::$client->execute($command);
        $this->assertEquals('F5E648DD640026EC18956E6BF0384FBDA2E0E5D7', $result['SubscriptionCertificateThumbprint']);

        $lastRequest = self::$history->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals('/a6c26c09-324b-41ea-aecb-00807a60be8a/certificates/cert1', $lastRequest->getPath());
    }

    /**
     * This covers certificates.get
     */
    public function testGetNotFound()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Certificates/GetNotFoundResponse');
        self::$mock->addResponse($response);

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('certificates.get', array('thumbprint' => 'cert1'));

        try {
            $result = self::$client->execute($command);
        } catch (CommandClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(404, $response->getStatusCode());

            $xml = $response->xml();
            $this->assertEquals($xml->Code, "ResourceNotFound");
            $this->assertEquals($xml->Message, "The certificate was not found.");
        }
    }
}