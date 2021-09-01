<?php
require './vendor/autoload.php';

use RetailCrm\Api\Factory\SimpleClientFactory;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;


ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$client = SimpleClientFactory::createClient('https://superposuda.retailcrm.ru/', 'QlnRWTTWw9lv3kjxy1A8byjUmBQedYqb');

//$request = new \RetailCrm\Api\Model\Request\Orders\OrdersRequest();
//$request->page = 1;

try {
    $response = $client->orders->list();
    print_r($response);
} catch (ApiExceptionInterface $exception) {
    echo sprintf(
        'Error from RetailCRM API (status code: %d): %s',
        $exception->getStatusCode(),
        $exception->getMessage()
    );

    if (count($exception->getErrorResponse()->errors) > 0) {
        echo PHP_EOL . 'Errors: ' . implode(', ', $exception->getErrorResponse()->errors);
    }

    return;
}
