<?php
class Supplier8 {
    public ?string $id;
    public ?string $name;
    public ?string $phone;
    public ?string $email;
    public ?string $address;
    public int $totalImportCount = 0;
    public int $totalImportQty = 0;
    public float $totalImportValue = 0.0;

    public function __construct(
        ?string $id = null,
        ?string $name = null,
        ?string $phone = null,
        ?string $email = null,
        ?string $address = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->address = $address;
    }
}
