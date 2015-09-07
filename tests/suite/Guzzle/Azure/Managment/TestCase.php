<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Managment;

use Guzzle\Azure\Managment\Client;

use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;

use org\bovigo\vfs\vfsStream;

/**
 * @author Gordon Franke <info@nevalon.de>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mock
     */
    static protected $mock;

    /**
     * @var Mock
     */
    static protected $history;

    /**
     * @var ManagmentClient
     */
    static protected $client;

    static public function setUpBeforeClass()
    {
        self::$client = Client::factory(array('subscription_id' => 'a6c26c09-324b-41ea-aecb-00807a60be8a', 'key_path' => vfsStream::url('root/cert.pem')));

        $emitter = self::$client->getHttpClient()->getEmitter();
        self::$mock = new Mock();
        $emitter->attach(self::$mock);

        self::$history = new History();
        $emitter->attach(self::$history);
    }
}
