<?php
require_once __DIR__ . '/Supplier8.php';
require_once __DIR__ . '/User8.php';
require_once __DIR__ . '/ImportInvoiceDetail8.php';

class ImportInvoice8 {
    public ?string $id;
    public ?Supplier8 $supplier;
    public ?string $importDate;
    public ?User8 $staff;
    public array $listDetail = [];
    public int $totalQty = 0;
    public float $totalValue = 0.0;

    public function __construct(
        ?string $id = null,
        ?Supplier8 $supplier = null,
        ?string $importDate = null,
        ?User8 $staff = null
    ) {
        $this->id = $id;
        $this->supplier = $supplier;
        $this->importDate = $importDate;
        $this->staff = $staff;
    }
}
