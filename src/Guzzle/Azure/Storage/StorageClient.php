<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Storage;

use Guzzle\Common\Event;
use Guzzle\Service\Client;
use Guzzle\Service\Inspector;
use Guzzle\Service\Description\ServiceDescription;

/**
 * Client for interacting with Azure Storage API
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class StorageClient extends Client
{
    /**
     * Factory method to create a new StorageClient
     *
     * @param array|Collection $config Configuration data. Array keys:
     *    base_url     - Base URL of web service
     *    account_name - Storage account name
     *    account_key  - Storage account key (primary or secondary)
     *
     * @return StorageClient
     */
    public static function factory($config = array())
    {
        $default = array(
            'base_url' => 'https://{account_name}.blob.core.windows.net/'
        );
        $required = array('base_url', 'account_name', 'account_key');
        $config = Inspector::prepareConfig($config, $default, $required);

        $client = new self(
            $config->get('base_url'),
            $config->get('account_name'),
            $config->get('account_key')
        );
        $client->setConfig($config);

        $description = ServiceDescription::factory(__DIR__ . DIRECTORY_SEPARATOR . 'client.xml');
        $client->setDescription($description);

        $client->getEventDispatcher()->addListener('request.before_send', function(Event $event) use ($client) {
            $request = $event['request'];
            // fix squid does not support Expect 100
            $request->removeHeader('Expect');
            $request->addHeader('x-ms-version', '2009-09-19');
            $request->addHeader('Date', gmdate('D, d M Y H:i:s T', time()));

            // compute signature
            $sign = $request->getMethod() . "\n";

            // sign header
            foreach (array('Content-Encoding', 'Content-Language', 'Content-Length', 'Content-MD5', 'Content-Type', 'Date', 'If-Modified-Since', 'If-Match', 'If-None-Match', 'If-Unmodified-Since', 'Range') as $header) {
                $sign .= $request->getHeader($header) . "\n";
            }

            // canonical header
            $canonicalizedHeaders = array();
            $headers = $request->getHeaders();
            foreach ($headers as $header => $values) {
                if ('x-ms-' === substr($header, 0, 5)) {
                    foreach ($values as $value) {
                        $canonicalizedHeaders[] = strtolower($header).':'.$value;
                    }
                }
            }
            sort($canonicalizedHeaders);
            $sign .= implode("\n", $canonicalizedHeaders) . "\n";

            // canonicalized resource
            $canonicalizedResource = '/'. $client->accountName . $request->getPath();

            $params = $request->getQuery()->getAll();
            ksort($params);

            foreach ($params as $key => $value) {
                // Grouping query parameters
                $values = explode(',', $value);
                sort($values);
                $separated = implode(',', $values);

                $canonicalizedResource .= "\n" . rawurldecode(strtolower($key) . ':' . $separated);
            }
            $sign .= $canonicalizedResource;

            // hash sign
            $signature = base64_encode(hash_hmac('sha256', $sign, base64_decode($client->accountKey), true));

            $event['request']->addHeader('Authorization', sprintf('SharedKey %s:%s', $client->accountName, $signature));
        });

        return $client;
    }

    /**
     * Client constructor
     *
     * @param string $baseUrl     Base URL of the web service
     * @param string $accountName Azure storage account name
     * @param string $aacountKey  Azure storage account key
     */
    public function __construct($baseUrl, $accountName, $accountKey)
    {
        parent::__construct($baseUrl);

        $this->accountName = $accountName;
        $this->accountKey = $accountKey;
    }
}
