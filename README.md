# Biano Star

## Installation

Install with [Composer](https://getcomposer.org/):

```
composer require biano/star-php
```

## Requirements

* This library uses [HTTPlug](http://httplug.io/) for HTTP client abstraction.
* This library uses [HTTP Factories](https://www.php-fig.org/psr/psr-17/) for manipulating HTTP requests.

You will need to install implementations of these abstraction layers to be able to use this library.

## Create an order

```php
use Biano\Star\Item;
use Biano\Star\Order;
use DateTimeImmutable;

$items = [
    new Item('item-1', 1, 1230.50),
    new Item('item-2', 3, 234.90),
];
$order = new Order('order-123', 1935.20, 'CZK', 'user@your-eshop.com', new DateTimeImmutable('+1 week'), ...$items);
```

The `Biano\Star\Item` constructor has the following signature: `public function __construct(string $id, int $quantity, float $unitPrice, ?string $name = null, ?string $image = null);`
* `$id` is your internal ID of the product, that you send to Biano in your product feed.
* `$quantity` is the quantity of this product in this order.
* `$unitPrice` is the price for one unit of this product.
* `$name` is an optional parameter with the name of the product.
* `$image` is an optional URL of an image of the product.

The `Biano\Star\Order` constructor has the following signature: `public function __construct(string $id, float $price, string $currency, ?string $customerEmail, ?DateTimeImmutable $shippingDate, Biano\Star\Item ...$items)`
* `$id` is your internal ID of this order.
* `$price` is the total price of this order.
* `$currency` is the [ISO 4217](https://en.wikipedia.org/wiki/ISO_4217) currency code of this order.
* `$customerEmail` is the email of the customer. This parameter can be `null`, in which case the order won't be tracked by Biano Star.
* `$shippingDate` is the expected shipping date of this order. This parameter can be `null`, in which case you should then use alternative ways to specify the shipping date. For more information, consult the Biano Star manual.
* `$items` is an array of order items. Notice the usage of the spread operator (`...`), as this parameter is variadic.

## Send order to Biano

```php
use Biano\Star\Project;
use Biano\Star\Star;
use Biano\Star\Version;

$star = new Star($httpClient, $requestFactory, $streamFactory); 
$response = $star->createPurchase(Project::cz(), Version::v1(), 'your-merchant-id', 'current-url', $order);
```

Choose the correct country of your eshop, supply a version parameter (currently only `v1` is supported), your eshop's Merchant ID, current URL of the page the order is being created at (this is usually something like the last step of your shopping cart, or the "Thank you" page on return from the payment gate), and the order.

## Update the shipping date

**This feature must be enabled by Biano. Contact your Biano partner first!**

```php
use Biano\Star\Project;
use Biano\Star\Star;

$star = new Star($httpClient, $requestFactory, $streamFactory);
$response = $star->updateShippingDate(Project::cz(), 'your-merchant-id', 'order-123', new DateTimeImmutable('2022-06-08'));
```

Choose the correct country of your eshop, supply your eshop's Merchant ID, the order ID, and the new shipping date.

You can update the shipping date as many times as you want.

