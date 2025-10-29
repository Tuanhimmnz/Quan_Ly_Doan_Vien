<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');

require_once __DIR__ . '/../../functions/khoa_functions.php';
require_once __DIR__ . '/../../handle/khoa_process.php';

// Bắt buộc phải có id trên URL để biết đang sửa khoa nào
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../khoa.php?error=Không tìm thấy khoa / viện");
    exit;
}

$id = $_GET['id'];
$khoa = handleGetKhoaById($id);

if (!$khoa) {
    header("Location: ../khoa.php?error=Khoa / Viện không tồn tại");
    exit;
}
$pageTitle = 'Chỉnh sửa Khoa / Viện';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container mt-3">
    <h3 class="mt-3 mb-4 text-center">CHỈNH SỬA KHOA / VIỆN</h3>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card">
                <div class="card-body">
                    <form action="../../handle/khoa_process.php" method="POST">
                        <!-- action=edit -> handleEditKhoa() -->
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($khoa['id']); ?>">

                        <div class="mb-3">
                            <label class="form-label">Tên Khoa / Viện</label>
                            <input
                                type="text"
                                class="form-control"
                                name="ten_khoa"
                                value="<?= htmlspecialchars($khoa['ten_khoa']); ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trưởng khoa / Viện trưởng</label>
                            <input
                                type="text"
                                class="form-control"
                                name="truong_khoa"
                                value="<?= htmlspecialchars($khoa['truong_khoa'] ?? ''); ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SĐT liên hệ</label>
                            <input
                                type="text"
                                class="form-control"
                                name="sdt_lien_he"
                                value="<?= htmlspecialchars($khoa['sdt_lien_he'] ?? ''); ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email liên hệ</label>
                            <input
                                type="email"
                                class="form-control"
                                name="email_lien_he"
                                value="<?= htmlspecialchars($khoa['email_lien_he'] ?? ''); ?>"
                            >
                        </div>

                        <!-- mo_ta sau cùng -->
                        <div class="mb-3">
                            <label class="form-label">Mô tả / Ghi chú</label>
                            <textarea
                                class="form-control"
                                name="mo_ta"
                                rows="3"
                            ><?= htmlspecialchars($khoa['mo_ta'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="../khoa.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Hủy</a>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Cập nhật</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
