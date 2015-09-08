Guzzle Azure
============

Azure plugin for the guzzle php http client.

[![Build Status](https://secure.travis-ci.org/gimler/guzzle-azure.png?branch=master)](http://travis-ci.org/gimler/guzzle-azure)
[![Dependency Status](https://www.versioneye.com/user/projects/525fcb36632bac28670000ad/badge.png)](https://www.versioneye.com/user/projects/525fcb36632bac28670000ad)

## Installation

If you are using Composer, and you should, just reference the plugin in your composer.json file:

``` sh
composer require "pazure/guzzle-azure"
```

## Available Clients

* Managment Client
  * Certificates
  * Locations
  * Operations
* Storage Client
  * Container
  * Blob

## FAQ

### create cert

Create self-signed certificate (see [https://www.openssl.org/docs/apps/req.html])
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout pazure2.pem -out pazure2.pem

(see [https://www.openssl.org/docs/apps/x509.html])
openssl x509 -outform der -in pazure2.pem -out pazure2.cer