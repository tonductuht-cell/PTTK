<?php
require_once __DIR__ . '/Product8.php';

class InvoiceDetail8 {
    public ?int $id;
    public ?string $invoiceId;
    public ?Product8 $product;
    public int $qty = 0;
    public float $price = 0.0;

    public function __construct(
        ?int $id = null,
        ?string $invoiceId = null,
        ?Product8 $product = null,
        int $qty = 0,
        float $price = 0.0
    ) {
        $this->id = $id;
        $this->invoiceId = $invoiceId;
        $this->product = $product;
        $this->qty = $qty;
        $this->price = $price;
    }

    public function getSubtotal(): float {
        return $this->qty * $this->price;
    }
}
