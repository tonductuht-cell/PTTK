<?php
class User8 {
    public ?string $id;
    public ?string $username;
    public ?string $password;
    public ?string $role;
    public ?string $name;
    public ?string $phone;
    public ?string $email;
    public ?string $address;

    public function __construct(
        ?string $id = null,
        ?string $username = null,
        ?string $password = null,
        ?string $role = null,
        ?string $name = null,
        ?string $phone = null,
        ?string $email = null,
        ?string $address = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->address = $address;
    }
}
