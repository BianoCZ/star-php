<?php

declare(strict_types = 1);

namespace Biano\Star;

use DateTimeImmutable;
use JsonSerializable;

final class Order implements JsonSerializable
{

    private string $id;

    private float $price;

    private string $currency;

    private ?string $customerEmail;

    private ?DateTimeImmutable $shippingDate;

    /** @var \Biano\Star\Item[] */
    private array $items;

    public function __construct(
        string $id,
        float $price,
        string $currency,
        ?string $customerEmail,
        ?DateTimeImmutable $shippingDate,
        Item ...$items
    ) {
        $this->id = $id;
        $this->price = $price;
        $this->currency = $currency;
        $this->customerEmail = $customerEmail;
        $this->shippingDate = $shippingDate;
        $this->items = $items;
    }

    /**
     * @return array{
     *  id: string,
     *  order_price: float,
     *  currency: string,
     *  items: \Biano\Star\Item[],
     *  source: 'php',
     *  customer_email?: string,
     *  shipping_date?: string
     * }
     */
    public function jsonSerialize(): array
    {
        $data = [
            'id' => $this->id,
            'order_price' => $this->price,
            'currency' => $this->currency,
            'items' => $this->items,
            'source' => 'php',
        ];

        if ($this->customerEmail !== null) {
            $data['customer_email'] = $this->customerEmail;
        }

        if ($this->shippingDate !== null) {
            $data['shipping_date'] = $this->shippingDate->format('Y-m-d');
        }

        return $data;
    }

}
