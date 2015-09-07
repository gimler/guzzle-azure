<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Tests\Storage;

use Guzzle\Azure\Storage\Client;

use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;

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
        self::$client = Client::factory(array('account_name' => 'guzzle-azure', 'account_key' => 'ga8MH/CSJgGRQeg+EJ1TkoD58SLmvJX3oLtLDIsLCbE5IteVPLA1GlQnK2m2EFPMOKadVEP3HZWokQjpGP03vA=='));

        $emitter = self::$client->getHttpClient()->getEmitter();
        self::$mock = new Mock();
        $emitter->attach(self::$mock);

        self::$history = new History();
        $emitter->attach(self::$history);
    }
}
