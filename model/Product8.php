<?php
class Product8 {
    public ?string $id;
    public ?string $name;
    public ?string $type;
    public ?string $unit;
    public float $buyPrice = 0.0;
    public float $sellPrice = 0.0;
    public int $stock = 0;

    public function __construct(
        ?string $id = null,
        ?string $name = null,
        ?string $type = null,
        ?string $unit = null,
        float $buyPrice = 0.0,
        float $sellPrice = 0.0,
        int $stock = 0
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->unit = $unit;
        $this->buyPrice = $buyPrice;
        $this->sellPrice = $sellPrice;
        $this->stock = $stock;
    }
}
