# AuthIO-Service
A small Web app designed to be used as a service for consummation by the Android client through Retrofit. Uses a WAMP server hosted on a local machine and accessed through a public WAN IP on port 8080. Must be put under the name AuthIO-Service in the "www" folder of the WAMP server in order to be recognised by the client Android app.

Requires Composer and [firebase/php-jwt](https://github.com/firebase/php-jwt) dependency. Need to also run `composer install` upon project cloning to facilitate dependency autoloading.

Requires private/public key pair for SSL to be put in a keys directory in the API directory. Please contact developer if you wish to contribute and they'll be supplied to you.