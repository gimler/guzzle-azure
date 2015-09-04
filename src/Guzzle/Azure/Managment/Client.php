<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Managment;

use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Collection;
use GuzzleHttp\Command\Guzzle\Description;
use Webbj74\JSDL\Loader\ServiceDescriptionLoader;
use Exception;

/**
 * Client for interacting with Azure Managment API
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class Client extends GuzzleClient
{
    /**
     * Factory method to create a new AzureClient
     *
     * @param array|Collection $config Configuration data. Array keys:
     *    base_url        - Base URl of web service
     *    subscription_id - azure subscription id
     *    key_path        - certificate key path
     *
     * @return ManagmentClient
     */
    public static function factory($config = array())
    {
        $default = array(
            'base_uri' => 'https://management.core.windows.net/{subscription_id}/'
        );
        $required = array('base_uri', 'subscription_id', 'key_path');
        $config = Collection::fromConfig($config, $default, $required);

        $client = new self(
            $config->get('base_uri'),
            $config->get('subscription_id'),
            $config->get('key_path')
        );

        return $client;
    }

    /**
     * Client constructor
     *
     * @param string $baseUrl Base URL of the web service
     * @param string $subscriptionId Azure subscription id
     * @param string $keyPath Certificates key path
     *
     * @throws Exception if key not exists
     * @throws Exception if key is not readable
     */
    public function __construct($baseUrl, $subscriptionId, $keyPath)
    {
        if (!file_exists($keyPath)) {
            throw new \Exception(sprintf('certificate key file `%s` does not exist', $keyPath));
        } else if (!is_readable($keyPath)) {
            throw new \Exception(sprintf('Could not read certificate key file `%s`', $keyPath));
        }

        //TODO: key_path relative vs. absolute paths
        //TODO: check expect option nesaccary
        $client = new GuzzleHttpClient(array(
            'base_url' => [
                $baseUrl,
                ['subscription_id' => $subscriptionId]
            ],
            'defaults' => [
                'headers' => array(
                    'x-ms-version' => '2015-04-01'
                ),
                'cert' => $keyPath
//              'expect' => true,
            ]
        ));

        $jsdlLoader = new ServiceDescriptionLoader();
        $description = new Description($jsdlLoader->load(__DIR__ . DIRECTORY_SEPARATOR . 'client.json'));

        parent::__construct($client, $description, []);
    }

    public function getLocations()
    {
        return $this->execute($this->getCommand('locations.list'));
    }

    public function getOperation($id)
    {
        return $this->execute($this->getCommand('operations.get', array('request_id' => $id)));
    }
}
