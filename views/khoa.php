<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/common_ui.php'; // [FITDNU-ADD]
checkLogin(__DIR__ . '/../index.php');
require_once __DIR__ . '/../handle/khoa_process.php';
$khoas = handleGetAllKhoa();
$pageTitle = 'Quản lý Khoa / Viện - Đại học Đại Nam 2025';
require __DIR__ . '/../includes/header.php';
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll">
    <div class="card-header d-flex align-items-center justify-content-between">
      <h3 class="m-0">DANH SÁCH KHOA / VIỆN</h3>
      <a href="khoa/create_khoa.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Thêm Khoa / Viện</a>
    </div>

    <div class="card-body">
      <!-- [FITDNU-ADD] Tìm kiếm Khoa/Viện -->
      <form method="get" class="row g-2 mb-3">
        <div class="col-md-8">
          <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control" placeholder="Tìm theo tên khoa, trưởng khoa, email, SĐT">
        </div>
        <div class="col-md-4 d-flex gap-2">
          <button class="btn btn-outline-primary btn-sm">Tìm kiếm</button>
          <a href="khoa.php" class="btn btn-outline-secondary btn-sm">Làm mới</a>
        </div>
      </form>
      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success reveal-on-scroll">
          <?= htmlspecialchars($_GET['success']) ?>
          <button type="button" class="btn-close" aria-label="Đóng">×</button>
        </div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger reveal-on-scroll">
          <?= htmlspecialchars($_GET['error']) ?>
          <button type="button" class="btn-close" aria-label="Đóng">×</button>
        </div>
      <?php endif; ?>

      <div class="table-responsive reveal-on-scroll">
        <table class="table table-striped table-hover align-middle" style="table-layout:fixed;">
          <thead>
            <tr>
              <th>#</th>
              <th>Tên Khoa / Viện</th>
              <th>Trưởng khoa / Viện trưởng</th>
              <th>SĐT liên hệ</th>
              <th>Email liên hệ</th>
              <th style="width:280px;">Mô tả</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
              $per = 15;
              $p = paginate_array($khoas, $page, $per);
              $rows = $p['data'];
              $meta = $p['meta'];
              foreach ($rows as $index => $khoa): ?>
              <tr>
                <td><?= ($meta['per_page']*($meta['page']-1)) + $index + 1 ?></td>
                <td><?= htmlspecialchars($khoa['ten_khoa']) ?></td>
                <td><?= htmlspecialchars($khoa['truong_khoa'] ?? '') ?></td>
                <td><?= htmlspecialchars($khoa['sdt_lien_he'] ?? '') ?></td>
                <td><?= htmlspecialchars($khoa['email_lien_he'] ?? '') ?></td>
                <td style="white-space:pre-wrap; word-wrap:break-word;">
                  <?= nl2br(htmlspecialchars($khoa['mo_ta'] ?? '')); ?>
                </td>
                <td>
                  <a href="khoa/edit_khoa.php?id=<?= $khoa['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i> Sửa</a>
                  <a href="../handle/khoa_process.php?action=delete&id=<?= $khoa['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa khoa / viện này?');">
                    <i class="fa fa-trash"></i> Xóa
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>

            <?php if (count($khoas) === 0): ?>
              <tr>
                <td colspan="7" class="text-center text-muted">Chưa có khoa / viện nào.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
        <div class="mt-2">
          <?= render_pagination('khoa.php', $meta['page'] ?? 1, $meta['pages'] ?? 1, $_GET) ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
