<?php
session_start();
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
    header('Location: ../LoginView8.php');
    exit;
}
$user = $_SESSION['user'];
require_once __DIR__ . '/../../dao/SupplierDAO8.php';

$startDate = isset($_GET['startDate']) && is_string($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) && is_string($_GET['endDate']) ? $_GET['endDate'] : '';
$submitted = array_key_exists('startDate', $_GET) || array_key_exists('endDate', $_GET);
$validRange = false;
$error = '';
$listSupplier = [];
$startValue = null;
$endValue = null;

if ($submitted) {
    $startValue = DateTime::createFromFormat('!Y-m-d', $startDate);
    $startErrors = DateTime::getLastErrors();
    $endValue = DateTime::createFromFormat('!Y-m-d', $endDate);
    $endErrors = DateTime::getLastErrors();
    $validStart = $startValue && $startValue->format('Y-m-d') === $startDate
        && ($startErrors === false || ($startErrors['warning_count'] === 0 && $startErrors['error_count'] === 0));
    $validEnd = $endValue && $endValue->format('Y-m-d') === $endDate
        && ($endErrors === false || ($endErrors['warning_count'] === 0 && $endErrors['error_count'] === 0));

    if ($validStart && $validEnd && $startDate <= $endDate) {
        try {
            $listSupplier = (new SupplierDAO8())->getListSupplier($startDate, $endDate);
            $validRange = true;
        } catch (PDOException $exception) {
            error_log('Không thể tải thống kê nhà cung cấp: ' . $exception->getMessage());
            $error = 'Không thể tải dữ liệu thống kê. Vui lòng kiểm tra kết nối cơ sở dữ liệu.';
        }
    } else {
        $error = 'Vui lòng nhập khoảng thời gian hợp lệ; ngày bắt đầu phải trước hoặc bằng ngày kết thúc.';
    }
}

$totalImportValue = 0.0;
foreach ($listSupplier as $supplier) {
    $totalImportValue += $supplier->totalImportValue;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ShopMan – Thống kê nhà cung cấp</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="standalone-screen">
<header class="app-navbar">
  <a href="ManagerMainMenu8.php" class="navbar-brand">ShopMan</a>
  <div class="navbar-user-box">
    <div class="navbar-user-info"><span class="name"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span></div>
    <a href="../Logout8.php" class="btn-nav-logout">Đăng xuất</a>
  </div>
</header>
<main class="app-container">
  <div class="screen-header-box pttk-style">
    <div class="screen-header-left">
      <h2>THỐNG KÊ NHÀ CUNG CẤP THEO LƯỢNG HÀNG NHẬP</h2>
      <?php if ($validRange): ?>
        <p>Khoảng thời gian: Từ <strong><?= $startValue->format('d/m/Y') ?></strong> đến <strong><?= $endValue->format('d/m/Y') ?></strong></p>
      <?php endif; ?>
    </div>
  </div>
  <div class="filter-card">
    <form method="get" action="SupplierImportStatisticView8.php">
      <div class="filter-row">
        <div class="form-group"><label for="startDate">Từ ngày</label><input id="startDate" type="date" name="startDate" value="<?= htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8') ?>" required></div>
        <div class="form-group"><label for="endDate">Đến ngày</label><input id="endDate" type="date" name="endDate" value="<?= htmlspecialchars($endDate, ENT_QUOTES, 'UTF-8') ?>" required></div>
        <div class="form-group align-end"><button type="submit" class="btn-primary">Thống kê</button></div>
      </div>
    </form>
    <?php if ($error !== ''): ?><p role="alert" style="color:#c53030;margin-top:.75rem;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  </div>
  <?php if ($validRange): ?>
  <section class="data-card">
    <div class="card-header-row"><h3>Danh sách nhà cung cấp theo sản lượng nhập</h3><span class="badge"><?= count($listSupplier) ?> đối tác</span></div>
    <div class="table-wrapper">
      <table class="data-table">
        <thead><tr><th>Mã NCC</th><th>Tên nhà cung cấp</th><th>Điện thoại</th><th style="text-align:center;">Số lần nhập</th><th style="text-align:center;">Tổng SL nhập</th><th style="text-align:right;">Tổng giá trị nhập</th><th>Thao tác</th></tr></thead>
        <tbody>
          <?php if (empty($listSupplier)): ?>
            <tr><td colspan="7" class="empty-state"><p>Không có dữ liệu nhập hàng nào trong khoảng thời gian đã chọn.</p></td></tr>
          <?php else: foreach ($listSupplier as $supplier): ?>
            <?php $historyUrl = 'ImportHistoryView8.php?supplierId=' . urlencode($supplier->id) . '&startDate=' . urlencode($startDate) . '&endDate=' . urlencode($endDate); ?>
            <tr>
              <td><strong><?= htmlspecialchars($supplier->id, ENT_QUOTES, 'UTF-8') ?></strong></td>
              <td><?= htmlspecialchars($supplier->name, ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($supplier->phone ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
              <td style="text-align:center;font-weight:600;"><span style="display:inline-block;min-width:5ch;text-align:right;font-variant-numeric:tabular-nums;"><?= $supplier->totalImportCount ?></span></td>
              <td style="text-align:center;font-weight:700;color:#2b6cb0;"><span style="display:inline-block;min-width:5ch;text-align:right;font-variant-numeric:tabular-nums;"><?= number_format($supplier->totalImportQty, 0, ',', '.') ?></span></td>
              <td style="text-align:right;font-weight:700;color:#276749;"><?= number_format($supplier->totalImportValue, 0, ',', '.') ?> ₫</td>
              <td><a href="<?= $historyUrl ?>" class="btn-primary" style="text-decoration:none;font-size:.82rem;padding:.45rem .9rem;display:inline-block;">Click để xem</a></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
        <tfoot><tr style="background:#f7fafc;font-weight:800;border-top:2px solid #cbd5e0;">
          <td colspan="4"></td><td style="text-align:right;padding:1rem;">Tổng:</td><td style="text-align:right;color:#276749;font-size:1.05rem;padding:1rem;"><?= number_format($totalImportValue, 0, ',', '.') ?> ₫</td><td></td>
        </tr></tfoot>
      </table>
    </div>
  </section>
  <?php endif; ?>
  <div style="margin-top:1rem;"><a href="ManagerMainMenu8.php" class="btn-primary" style="text-decoration:none;background:linear-gradient(135deg,#2b6cb0,#1a365d);font-weight:700;border-radius:10px;padding:.65rem 1.2rem;">Quay lại</a></div>
</main>
</body>
</html>
