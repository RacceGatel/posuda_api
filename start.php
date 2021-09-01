<?php
require './vendor/autoload.php';

use RetailCrm\Api\Factory\SimpleClientFactory;
use RetailCrm\Api\Interfaces\ApiExceptionInterface;
use RetailCrm\Api\Model\Entity\Orders\Order;
use RetailCrm\Api\Model\Request\Store\ProductsRequest;
use RetailCrm\Api\Model\Filter\Store\ProductFilterType;
use RetailCrm\Api\Model\Request\Orders\OrdersCreateRequest;
use RetailCrm\Api\Model\Entity\Loyalty\OrderProduct;

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


$client = SimpleClientFactory::createClient('https://superposuda.retailcrm.ru/', 'QlnRWTTWw9lv3kjxy1A8byjUmBQedYqb');

$request = new ProductsRequest();
$request->filter = new ProductFilterType();
$request->filter->manufacturer = "Azalita";
$product = null;

try {
    $response = $client->store->products($request);
    foreach($response->products as $item) {
        if($item->article == "AZ105R") {
            $product = $item;
            break;
        }
    }
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

if($product != null) {
    $offer = new \RetailCrm\Api\Model\Entity\Orders\Items\Offer();
    $offer->externalId = $product->offers[0]->externalId;

    $orderProduct = new OrderProduct();
    $orderProduct->offer = $offer;

    $request = new OrdersCreateRequest();

    $order = new Order();
    $order->orderType = "fizik";
    $order->orderMethod = "test";
    $order->number = "04102000";
    $order->firstName = "Ильяс";
    $order->lastName = "Гиззатуллин";
    $order->patronymic = "Ильдусович";
    $order->items = [$orderProduct];
    $order->customFields = [
        "prim" => "тестовое задание"
    ];
    $order->status = 'trouble';
    $order->statusComment = 'https://github.com/RacceGatel/posuda_api';

    $request->order = $order;
    $request->site = "test";

    try {
        $response = $client->orders->create($request);
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
}
