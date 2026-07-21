<?php
declare(strict_types=1);

session_start();

// guard: chua login thi ve login
if (empty($_SESSION['auth'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/data.php';

// xu ly dat hang thu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'order') {
    $sku = trim($_POST['sku'] ?? '');
    $qty = (int)($_POST['qty'] ?? 0);

    // chi luu don khi sku ton tai that trong danh sach san pham
    $spDat = findProductBySku($productObjects, $sku);

    if ($spDat !== null && $qty > 0) {
        if (!isset($_SESSION['orders'])) {
            $_SESSION['orders'] = array();
        }
        $_SESSION['orders'][] = array(
            'sku' => $sku,
            'qty' => $qty,
            'at' => date('H:i:s')
        );
    }
    header('Location: dashboard.php');
    exit;
}

// lay category_id tu URL (neu co)
$catId = null;
if (isset($_GET['category_id'])) {
    $catId = (int)$_GET['category_id'];
}

// loc sp
$dsHienThi = filterByCategoryObjects($productObjects, $catId);

// Tinh tong kho va xep hang
$tongKho = inventoryValueFromObjects($productObjects);
$hangKho = rankInventory($tongKho);

// Tinh tong gia tri tung danh muc xuat bao cao (dung ham dung chung o data.php, khong lap tay o view)
$soLuongBanPhim = countByCategoryObjects($productObjects, 1);
$soLuongChuot = countByCategoryObjects($productObjects, 2);
$soLuongManHinh = countByCategoryObjects($productObjects, 3);

$tongBanPhim = sumValueByCategoryObjects($productObjects, 1);
$tongChuot = sumValueByCategoryObjects($productObjects, 2);
$tongManHinh = sumValueByCategoryObjects($productObjects, 3);

$orders = $_SESSION['orders'] ?? array();

function renderProductRows(array $products, array $categoryMap): void {
    $stt = 1;
    foreach ($products as $sp) {
        $thanhTien = $sp->lineTotal();
        $muc = $sp->stockLevel();
        $tenDM = isset($categoryMap[$sp->categoryId]) ? $categoryMap[$sp->categoryId] : 'Khong xac dinh';

        echo '<tr>';
        echo '<td>' . $stt . '</td>';
        echo '<td>' . htmlspecialchars($sp->sku) . '</td>';
        echo '<td>' . htmlspecialchars($tenDM) . '</td>';
        echo '<td>' . htmlspecialchars($sp->name) . '</td>';
        echo '<td>' . $sp->price . '</td>';
        echo '<td>' . $sp->qty . '</td>';
        echo '<td>' . $thanhTien . '</td>';
        echo '<td>' . $muc . '</td>';
        echo '</tr>';

        $stt = $stt + 1;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard - <?php echo date('Y'); ?></title>
    <style>
        table { border: 1px solid black; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 5px; border: 1px solid black; }
        th { background: #ccc; }
        a { margin-right: 10px; }
        .logout { color: red; }
    </style>
</head>
<body>

<h2>Xin chao, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
<p>
    <a href="dashboard.php">Tat ca</a>
    <a href="?category_id=1">Ban phim</a>
    <a href="?category_id=2">Chuot</a>
    <a href="?category_id=3">Man hinh</a>
    <a href="logout.php" class="logout">Dang xuat</a>
</p>

<h3>Danh sach hang hoa</h3>
<table border="1">
    <tr>
        <th>STT</th>
        <th>Ma hang</th>
        <th>Phan loai</th>
        <th>Ten san pham</th>
        <th>Don gia</th>
        <th>So luong ton</th>
        <th>Thanh tien</th>
        <th>Muc ton</th>
    </tr>
    <?php renderProductRows($dsHienThi, $categoryMap); ?>
</table>

<p>Tong cong: <?php echo $tongKho; ?> VND</p>
<p>Quy mo kho: <?php echo $hangKho; ?></p>
<p>Co tat ca <?php echo count($dsHienThi); ?> mat hang</p>

<h3>Bao cao theo danh muc</h3>
<table border="1">
    <tr>
        <th>Danh muc</th>
        <th>So SP</th>
        <th>Tong gia tri</th>
    </tr>
    <tr>
        <td>Ban phim</td>
        <td><?php echo $soLuongBanPhim; ?></td>
        <td><?php echo $tongBanPhim; ?></td>
    </tr>
    <tr>
        <td>Chuot</td>
        <td><?php echo $soLuongChuot; ?></td>
        <td><?php echo $tongChuot; ?></td>
    </tr>
    <tr>
        <td>Man hinh</td>
        <td><?php echo $soLuongManHinh; ?></td>
        <td><?php echo $tongManHinh; ?></td>
    </tr>
</table>

<h3>Dat hang thu</h3>
<form method="post" action="dashboard.php">
    <input type="hidden" name="action" value="order">
    <p>
        <label>Chon san pham:</label>
        <select name="sku">
            <?php foreach ($productObjects as $p): ?>
                <option value="<?php echo htmlspecialchars($p->sku); ?>">
                    <?php echo htmlspecialchars($p->sku . ' - ' . $p->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label>So luong:</label>
        <input type="number" name="qty" min="1" value="1" required>
    </p>
    <p>
        <button type="submit">Dat thu</button>
    </p>
</form>

<?php if (count($orders) > 0): ?>
<h3>Danh sach dat hang</h3>
<table border="1">
    <tr>
        <th>STT</th>
        <th>Ma hang</th>
        <th>So luong</th>
        <th>Thoi gian</th>
    </tr>
    <?php
    $sttOrder = 1;
    foreach ($orders as $o):
    ?>
        <tr>
            <td><?php echo $sttOrder; ?></td>
            <td><?php echo htmlspecialchars($o['sku']); ?></td>
            <td><?php echo $o['qty']; ?></td>
            <td><?php echo $o['at']; ?></td>
        </tr>
    <?php
        $sttOrder = $sttOrder + 1;
    endforeach;
    ?>
</table>
<?php endif; ?>

</body>
</html>