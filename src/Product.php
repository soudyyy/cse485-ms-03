<?php
declare(strict_types=1);

class Product {
    public string $sku;
    public string $name;
    public int $categoryId;
    public int $price;
    public int $qty;

    public function __construct(string $sku, string $name, int $categoryId, int $price, int $qty) {
        $this->sku = $sku;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->price = $price;
        $this->qty = $qty;
    }

    public function lineTotal(): int {
        $gia = $this->price;
        $sl = $this->qty;
        return $gia * $sl;
    }

    public function stockLevel(): string {
        $soLuong = $this->qty;
        if ($soLuong >= 5) {
            return 'Du';
        }
        if ($soLuong >= 2) {
            return 'Sap het';
        }
        return 'Can nhap';
    }

    public function toArray(): array {
        return array(
            'sku' => $this->sku,
            'name' => $this->name,
            'category_id' => $this->categoryId,
            'price' => $this->price,
            'qty' => $this->qty,
            'line_total' => $this->lineTotal(),
            'stock' => $this->stockLevel(),
        );
    }
}