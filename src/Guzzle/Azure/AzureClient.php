<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure;

use Guzzle\Service\Client;
use Guzzle\Common\Event;
use Guzzle\Service\Inspector;
use Guzzle\Service\Description\ServiceDescription;

/**
 * Client for interacting with Azure Managment API
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class AzureClient extends Client
{
    /**
     * Factory method to create a new AzureClient
     *
     * @param array|Collection $config Configuration data. Array keys:
     *    base_url        - Base URL of web service
     *    subscription_id - azure subscription id
     *    key_path        - certificate key path
     *
     * @return ForumClient
     */
    public static function factory($config = array())
    {
        $default = array(
            'base_url' => 'https://management.core.windows.net/{subscription_id}/'
        );
        $required = array('base_url', 'subscription_id', 'key_path');
        $config = Inspector::prepareConfig($config, $default, $required);

        $client = new self(
            $config->get('base_url'),
            $config->get('subscription_id'),
            $config->get('key_path')
        );
        $client->setConfig($config);

//TODO: key_path file_exists and readable check
//TODO: key_path relative vs. absolute paths

        $description = ServiceDescription::factory(__DIR__ . DIRECTORY_SEPARATOR . 'client.xml');
        $client->setDescription($description);

        // fix squid does not support Expect 100
        $client->getEventDispatcher()->addListener('request.before_send', function(Event $event) {
            $event['request']->removeHeader('Expect');
            $event['request']->addHeader('x-ms-version', '2012-03-01');
        });
        $client->getEventDispatcher()->addListener('client.create_request', function(Event $event) {
            $event['request']->getCurlOptions()
                ->set(CURLOPT_SSLCERT, $event['client']->keyPath);
        });

        return $client;
    }

    /**
     * Client constructor
     *
     * @param string $baseUrl        Base URL of the web service
     * @param string $subscriptionId Azure subscription id
     * @param string $keyPath        Certificate key path
     */
    public function __construct($baseUrl, $subscriptionId, $keyPath)
    {
        parent::__construct($baseUrl);

        $this->subscriptionId = $subscriptionId;
        $this->keyPath = $keyPath;
    }
}
