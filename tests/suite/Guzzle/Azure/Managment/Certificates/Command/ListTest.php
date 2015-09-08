<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Managment\Certificates\Command;

use Guzzle\Azure\Tests\Managment\TestCase;

/**
 * @author Gordon Franke <info@nevalon.de>
 */
class ListTest extends TestCase
{
    /**
     * This covers certificates.list
     */
    public function testList()
    {
        $response = file_get_contents(MOCK_BASE_PATH . '/Azure/Managment/Certificates/ListResponse');
        self::$mock->addResponse($response);

        /** @var $command \GuzzleHttp\Command\Command */
        $command = self::$client->getCommand('certificates.list');

        $result = self::$client->execute($command);
        $this->assertCount(1, $result["certificates"]);
        $this->assertEquals('F5E648DD640026EC18956E6BF0384FBDA2E0E5D7', $result["certificates"][0]['SubscriptionCertificateThumbprint']);

        $lastRequest = self::$history->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals('/a6c26c09-324b-41ea-aecb-00807a60be8a/certificates', $lastRequest->getPath());
    }
}

