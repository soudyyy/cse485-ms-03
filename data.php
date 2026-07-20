<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Category.php';
require_once __DIR__ . '/src/Product.php';

// mang danh muc object
$categoryObjects = array(
    new Category(1, 'Ban phim'),
    new Category(2, 'Chuot'),
    new Category(3, 'Man hinh'),
);

// mang 8 san pham object
$productObjects = array(
    new Product('KB-01', 'Keychron K2',       1, 3200000, 3),
    new Product('KB-02', 'Logitech MX Keys',  1, 1200000, 5),
    new Product('KB-03', 'Akko 3084',         1, 1010000, 2),
    new Product('MS-01', 'Logitech G102',     2, 450000,  10),
    new Product('MS-02', 'Razer DeathAdder',  2, 590000,  4),
    new Product('MS-03', 'SteelSeries Rival', 2, 250000,  8),
    new Product('MN-01', 'Dell P2419H',       3, 5500000, 2),
    new Product('MN-02', 'LG UltraFine',      3, 3900000, 1),
);

// map id -> ten danh muc
$categoryMap = array();
$soLuongDM = count($categoryObjects);
for ($i = 0; $i < $soLuongDM; $i++) {
    $categoryMap[$categoryObjects[$i]->id] = $categoryObjects[$i]->name;
}

// tinh tong gia tri kho hang
function inventoryValueFromObjects(array $products): int {
    $tong = 0;
    $n = count($products);
    for ($i = 0; $i < $n; $i++) {
        $tong = $tong + $products[$i]->lineTotal();
    }
    return $tong;
}

// xep hang quy mo kho
function rankInventory(int $totalValue): string {
    if ($totalValue < 15000000) {
        return 'Nho';
    }
    if ($totalValue < 35000000) {
        return 'Trung binh';
    }
    return 'Lon';
}

// loc san pham theo danh muc
function filterByCategoryObjects(array $products, ?int $categoryId): array {
    if ($categoryId === null) {
        return $products;
    }
    $ketQua = array();
    $soPhanTu = count($products);
    for ($i = 0; $i < $soPhanTu; $i++) {
        if ($products[$i]->categoryId == $categoryId) {
            $ketQua[] = $products[$i];
        }
    }
    return $ketQua;
}

// dem so san pham thuoc 1 danh muc
function countByCategoryObjects(array $products, int $categoryId): int {
    $dem = 0;
    $soPhanTu = count($products);
    for ($i = 0; $i < $soPhanTu; $i++) {
        if ($products[$i]->categoryId == $categoryId) {
            $dem = $dem + 1;
        }
    }
    return $dem;
}