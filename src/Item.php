<?php

declare(strict_types = 1);

namespace Biano\Star;

use JsonSerializable;

final class Item implements JsonSerializable
{

    private string $id;

    private int $quantity;

    private float $unitPrice;

    private ?string $name;

    private ?string $image;

    public function __construct(
        string $id,
        int $quantity,
        float $unitPrice,
        ?string $name = null,
        ?string $image = null
    ) {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->name = $name;
        $this->image = $image;
    }

    /**
     * @return array{
     *  id: string,
     *  quantity: int,
     *  unit_price: float,
     *  name?: string,
     *  image?: string
     * }
     */
    public function jsonSerialize(): array
    {
        $data = [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->image !== null) {
            $data['image'] = $this->image;
        }

        return $data;
    }

}
