<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Managment\Certificates\Command;

use Guzzle\Azure\Managment\Certificates\Certificate;

use Guzzle\Azure\Tests\Managment\TestCase;

use GuzzleHttp\Command\Exception\CommandClientException;

/**
 * @author Gordon Franke <info@nevalon.de>
 */
class AddTest extends TestCase
{
    /**
     * This covers certificates.add
     */
    public function testAdd()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Managment/Certificates/AddResponse');
        self::$mock->addResponse($response);

        $data = new Certificate(FIXTURES_BASE_PATH . '/pazure.cer');

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('certificates.add', array(
            'SubscriptionCertificatePublicKey' => $data->publicKey,
            'SubscriptionCertificateThumbprint' => $data->thumbprint,
            'SubscriptionCertificateData' => $data->data
        ));

        $result = self::$client->execute($command);

        $lastRequest = self::$history->getLastRequest();
        $this->assertEquals('POST', $lastRequest->getMethod());
        $this->assertEquals('/a6c26c09-324b-41ea-aecb-00807a60be8a/certificates', $lastRequest->getPath());

        $lastResponse = self::$history->getLastResponse();
        $this->assertEquals(201, $lastResponse->getStatusCode());
        $this->assertEquals('c0898c17ddbba261aa7e6416e35cf818', $lastResponse->getHeader('x-ms-request-id'));
    }

    /**
     * This covers certificates.add
     */
    public function testAddAllreadyExist()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Managment/Certificates/AddAllreadyExistResponse');
        self::$mock->addResponse($response);

        $data = new Certificate(FIXTURES_BASE_PATH . '/pazure.cer');

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('certificates.add', array(
            'SubscriptionCertificatePublicKey' => $data->publicKey,
            'SubscriptionCertificateThumbprint' => $data->thumbprint,
            'SubscriptionCertificateData' => $data->data
        ));

        try {
            $result = self::$client->execute($command);
        } catch (CommandClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(409, $response->getStatusCode());

            $xml = $response->xml();
            $this->assertEquals($xml->Code, "ConflictError");
            $this->assertEquals($xml->Message, "The specified certificate already exists for this subscription.");
        }
    }
}
