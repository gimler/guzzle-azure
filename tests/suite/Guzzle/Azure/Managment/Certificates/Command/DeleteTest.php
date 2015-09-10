<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Managment\Certificates\Command;

use Guzzle\Azure\Tests\Managment\TestCase;

use GuzzleHttp\Command\Exception\CommandClientException;

/**
 * @author Gordon Franke <info@nevalon.de>
 */
class DeleteTest extends TestCase
{
    /**
     * This covers certificates.delete
     */
    public function testDelete()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Managment/Certificates/DeleteResponse');
        self::$mock->addResponse($response);

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('certificates.delete', array('SubscriptionCertificateThumbprint' => 'cert1'));

        $result = self::$client->execute($command);

        $lastRequest = self::$history->getLastRequest();
        $this->assertEquals('DELETE', $lastRequest->getMethod());
        $this->assertEquals('/a6c26c09-324b-41ea-aecb-00807a60be8a/certificates/cert1', $lastRequest->getPath());

        $lastResponse = self::$history->getLastResponse();
        $this->assertEquals(200, $lastResponse->getStatusCode());
        $this->assertEquals('ec2c8031ddee466e90646634a5a39556', $lastResponse->getHeader('x-ms-request-id'));
    }

    /**
     * This covers certificates.delete
     */
    public function testDeleteNotFound()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Managment/Certificates/DeleteNotFoundResponse');
        self::$mock->addResponse($response);

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('certificates.delete', array('SubscriptionCertificateThumbprint' => 'cert1'));

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
