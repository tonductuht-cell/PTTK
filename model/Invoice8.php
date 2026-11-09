<?php
require_once __DIR__ . '/User8.php';
require_once __DIR__ . '/InvoiceDetail8.php';

class Invoice8 {
    public ?string $id;
    public ?User8 $customer;
    public ?string $saleDate;
    public ?string $deliveryAddress;
    public ?string $status;
    public array $listDetail = [];
    public array $history = [];
    public int $totalQty = 0;
    public float $totalValue = 0.0;

    public function __construct(
        ?string $id = null,
        ?User8 $customer = null,
        ?string $saleDate = null,
        ?string $deliveryAddress = null,
        ?string $status = null
    ) {
        $this->id = $id;
        $this->customer = $customer;
        $this->saleDate = $saleDate;
        $this->deliveryAddress = $deliveryAddress;
        $this->status = $status;
    }
}
