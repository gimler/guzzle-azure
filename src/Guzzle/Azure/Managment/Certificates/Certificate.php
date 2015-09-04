<?php

namespace Guzzle\Azure\Managment\Certificates;

class Certificate
{
    public $publicKey;

    public $thumbprint;

    public $data;

    public function __construct($certPath)
    {
        $certData = file_get_contents($certPath);

        $this->data = base64_encode($certData);

        /* Convert .cer to .pem, cURL uses .pem */
        $certData =  '-----BEGIN CERTIFICATE-----'.PHP_EOL
            .chunk_split(base64_encode($certData), 64, PHP_EOL)
            .'-----END CERTIFICATE-----'.PHP_EOL;

        $cert = openssl_x509_read($certData);

        $this->thumbprint = openssl_x509_fingerprint($cert);

        $key = openssl_pkey_get_details(openssl_pkey_get_public($cert))["key"];
        $key = str_replace("-----BEGIN PUBLIC KEY-----\n", '', $key);
        $key = str_replace("-----END PUBLIC KEY-----", '', $key);
        $key = str_replace("\n", '', $key);
        $key = substr($key, 32);
        $this->publicKey = $key;

        openssl_x509_free($cert);
    }
}
