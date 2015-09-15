<?php
/**
 * @package Guzzle Azure <https://github.com/gimler/guzzle-azure>
 * @license See the LICENSE file that was distributed with this source code.
 */

namespace Guzzle\Azure\Storage;

use Guzzle\Service\Loader\JsonLoader;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Collection;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\RequestEvents;

use Symfony\Component\Config\FileLocator;

/**
 * Client for interacting with Azure Storage API
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class Client extends GuzzleClient
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
            'base_uri' => 'https://{account_name}.blob.core.windows.net/'
        );
        $required = array('base_uri', 'account_name', 'account_key');
        $config = Collection::fromConfig($config, $default, $required);

        $client = new self(
            $config->get('base_uri'),
            $config->get('account_name'),
            $config->get('account_key')
        );

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
        $client = new GuzzleHttpClient(array(
            'base_url' => [
                $baseUrl,
                ['account_name' => $accountName]
            ],
            'defaults' => [
                'headers' => array(
                    'x-ms-version' => '2015-02-21',
                    'x-ms-date' => gmdate('D, d M Y H:i:s T', time())
                ),
            ]
        ));

        $emitter = $client->getEmitter();
        $emitter->on('before', function (BeforeEvent $event) use ($accountName, $accountKey) {
            $request = $event->getRequest();

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
            $canonicalizedResource = '/'. $accountName . $request->getPath();

            $params = $request->getQuery()->toArray();
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
            $signature = base64_encode(hash_hmac('sha256', $sign, base64_decode($accountKey), true));

            $request->addHeader('Authorization', sprintf('SharedKey %s:%s', $accountName, $signature));
        }, RequestEvents::SIGN_REQUEST);

        $this->locator = new FileLocator(array(__DIR__));
        $this->jsonLoader = new JsonLoader($this->locator);

        $description = $this->jsonLoader->load($this->locator->locate('client.json'));
        $description = new Description($description);

        parent::__construct($client, $description, []);
    }
}