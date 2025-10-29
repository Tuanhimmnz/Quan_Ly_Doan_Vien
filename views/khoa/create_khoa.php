<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
$pageTitle = 'Thêm Khoa / Viện mới';
require __DIR__ . '/../../includes/header.php';
?>
<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <h3 class="mt-3 mb-4 text-center">THÊM KHOA / VIỆN MỚI</h3>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" style="white-space:pre-line;">
                    <strong>❌ Lỗi:</strong> <?= htmlspecialchars($_GET['error']) ?>
                    <?php if (!empty($_GET['debug'])): ?>
                        <hr>
                        <div class="small text-muted">
                            <strong>Chi tiết kỹ thuật:</strong><br>
                            <?= nl2br(htmlspecialchars($_GET['debug'])) ?>
                        </div>
                    <?php endif; ?>
                    <button type="button" class="btn-close" aria-label="Đóng">×</button>
                </div>
            <?php endif; ?>

            <form action="../../handle/khoa_process.php" method="POST">
                <!-- action=create -> handleCreateKhoa() trong khoa_process.php -->
                <input type="hidden" name="action" value="create">

                <div class="mb-3">
                    <label class="form-label">Tên Khoa / Viện <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        class="form-control"
                        name="ten_khoa"
                        placeholder="Ví dụ: Khoa Công nghệ thông tin"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Trưởng khoa / Viện trưởng</label>
                    <input
                        type="text"
                        class="form-control"
                        name="truong_khoa"
                        placeholder="Ví dụ: TS. Nguyễn Văn A"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">SĐT liên hệ</label>
                    <input
                        type="text"
                        class="form-control"
                        name="sdt_lien_he"
                        placeholder="Ví dụ: 0988xxxxxx"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Email liên hệ</label>
                    <input
                        type="email"
                        class="form-control"
                        name="email_lien_he"
                        placeholder="Ví dụ: cntt@dainam.edu.vn"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả / Ghi chú</label>
                    <textarea
                        class="form-control"
                        name="mo_ta"
                        rows="3"
                        placeholder="Giới thiệu khoa, ngành đào tạo, định hướng..."
                    ></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Lưu</button>
                    <a href="../khoa.php" class="btn btn-secondary"><i class="fa fa-xmark"></i> Hủy</a>
                </div>
            </form>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
