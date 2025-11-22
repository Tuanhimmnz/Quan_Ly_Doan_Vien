<?php
// Trang Giới thiệu (public) – dùng header/footer chung
$pageTitle = 'Giới thiệu - Hệ thống Quản lý Đoàn viên';
require __DIR__ . '/includes/header.php';

require_once __DIR__ . '/functions/db_connection.php';
$dvCount = 0;
$activeCount = 0;
$leftCount = 0;
$lopCount = 0;
$suKienCount = 0;
$payCount = 0;
$rewardCount = 0;
$disciplineCount = 0;
$chiDoanStats = [];
$conn = getDbConnection();
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM doanvien");
if ($res && $row = mysqli_fetch_assoc($res)) {
  $dvCount = (int)($row['total'] ?? 0);
}
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM doanvien WHERE trang_thai IN ('Đang sinh hoạt','Hoạt động','Chuyển sinh hoạt')");
if ($res && $row = mysqli_fetch_assoc($res)) {
  $activeCount = (int)($row['total'] ?? 0);
}
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM doanvien WHERE trang_thai IN ('Đã rời đoàn','Rời khỏi','Nghỉ','Đã ra trường')");
if ($res && $row = mysqli_fetch_assoc($res)) {
  $leftCount = (int)($row['total'] ?? 0);
}
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM lop");
if ($res && $row = mysqli_fetch_assoc($res)) {
  $lopCount = (int)($row['total'] ?? 0);
}
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM su_kien");
if ($res && $row = mysqli_fetch_assoc($res)) {
  $suKienCount = (int)($row['total'] ?? 0);
}
// Ưu tiên bảng doanphi; nếu không có dữ liệu, fallback doan_phi_thu
$res = mysqli_query($conn, "SELECT COUNT(DISTINCT dp.doanvien_id) AS total FROM doanphi dp WHERE dp.da_nop = 1 OR (dp.so_tien_nop IS NOT NULL AND dp.so_tien_nop > 0)");
if ($res && $row = mysqli_fetch_assoc($res)) { $payCount = (int)($row['total'] ?? 0); }
if ($payCount === 0) {
  $res = @mysqli_query($conn, "SELECT COUNT(DISTINCT doan_vien_id) AS total FROM doan_phi_thu WHERE so_tien > 0");
  if ($res && $row = mysqli_fetch_assoc($res)) { $payCount = (int)($row['total'] ?? 0); }
}
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM khen_thuong_kyluat WHERE loai = 'Khen thưởng'");
if ($res && $row = mysqli_fetch_assoc($res)) {
  $rewardCount = (int)($row['total'] ?? 0);
}
$res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM khen_thuong_kyluat WHERE loai = 'Kỷ luật'");
if ($res && $row = mysqli_fetch_assoc($res)) {
  $disciplineCount = (int)($row['total'] ?? 0);
}
$res = mysqli_query($conn, "SELECT l.ten_lop AS ten, COUNT(*) AS tong FROM doanvien dv LEFT JOIN lop l ON dv.lop_id = l.id GROUP BY dv.lop_id, l.ten_lop ORDER BY tong DESC LIMIT 5");
if ($res) {
  while ($row = mysqli_fetch_assoc($res)) {
    $pct = $dvCount > 0 ? round($row['tong'] * 100 / $dvCount, 1) : 0;
    $chiDoanStats[] = ['ten' => $row['ten'] ?? 'Chưa phân lớp', 'tong' => (int)$row['tong'], 'pct' => $pct];
  }
}
mysqli_close($conn);
?>

<div class="container mt-3 reveal-on-scroll">
  <div class="card reveal-on-scroll" style="margin-bottom:16px;">
    <h2 style="margin:0 0 8px; color:#0072BC;">Giới thiệu</h2>
    <p class="text-muted" style="margin:0 0 12px;">
      Đoàn Thanh Niên Cộng Sản Hồ Chí Minh là tổ chức chính trị – xã hội của thanh niên Việt Nam,
      nơi rèn luyện, bồi dưỡng lý tưởng cách mạng, đạo đức, lối sống và kỹ năng cho thanh niên; góp phần
      xây dựng và bảo vệ Tổ quốc. Hệ thống này hỗ trợ công tác quản lý đoàn viên, lớp/chi đoàn, đánh giá – khen thưởng
      và đoàn phí một cách trực quan, đồng bộ.
    </p>
  </div>

  <div class="gt-stats reveal-on-scroll">
    <div class="stat-card primary">
      <div>
        <p>Tổng số đoàn viên</p>
        <h2><?= number_format($dvCount, 0, ',', '.') ?></h2>
      </div>
      <i class="fa-solid fa-users"></i>
    </div>
    <div class="stat-card info">
      <div>
        <p>Tổng chi đoàn</p>
        <h2><?= number_format($lopCount, 0, ',', '.') ?></h2>
      </div>
      <i class="fa-solid fa-people-line"></i>
    </div>
    <div class="stat-card accent">
      <div>
        <p>Sự kiện đã tạo</p>
        <h2><?= number_format($suKienCount, 0, ',', '.') ?></h2>
      </div>
      <i class="fa-solid fa-calendar-check"></i>
    </div>
    <div class="stat-card success">
      <div>
        <p>Đang hoạt động</p>
        <h2><?= number_format($activeCount, 0, ',', '.') ?></h2>
      </div>
      <i class="fa-solid fa-check-circle"></i>
    </div>
    <div class="stat-card danger">
      <div>
        <p>Đã rời khỏi</p>
        <h2><?= number_format($leftCount, 0, ',', '.') ?></h2>
      </div>
      <i class="fa-solid fa-person-walking-arrow-right"></i>
    </div>
    <div class="stat-card teal">
      <div>
        <p>Đã nạp đoàn phí</p>
        <h2><?= number_format($payCount, 0, ',', '.') ?></h2>
      </div>
      <i class="fa-solid fa-sack-dollar"></i>
    </div>
    <div class="stat-card gold">
      <div>
        <p>Đoàn viên được khen thưởng</p>
        <h2><?= number_format($rewardCount, 0, ',', '.') ?></h2>
      </div>
      <i class="fa-solid fa-medal"></i>
    </div>
    <div class="stat-card neutral">
      <div>
        <p>Đoàn viên bị kỷ luật</p>
        <h2><?= number_format($disciplineCount, 0, ',', '.') ?></h2>
      </div>
      <i class="fa-solid fa-scale-balanced"></i>
    </div>
  </div>

  <div class="card reveal-on-scroll">
    <h3 style="margin:0 0 12px; color:#0b5181;">Tỷ lệ theo chi đoàn (Top 5)</h3>
    <?php if (!empty($chiDoanStats)): ?>
      <div class="progress-list">
        <?php foreach ($chiDoanStats as $item): ?>
          <div class="progress-row">
            <div class="progress-label">
              <span class="dot"></span>
              <span><?= htmlspecialchars($item['ten']) ?></span>
            </div>
            <div class="progress-track">
              <div class="progress-fill" style="width: <?= $item['pct'] ?>%"></div>
            </div>
            <div class="progress-meta">
              <strong><?= $item['pct'] ?>%</strong>
              <small><?= (int)$item['tong'] ?> đoàn viên</small>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="text-muted">Chưa có số liệu đoàn viên theo chi đoàn.</div>
    <?php endif; ?>
  </div>

  <div class="card reveal-on-scroll">
    <h3 style="margin:0 0 12px; color:#0b5181;">Hình ảnh hoạt động</h3>

    <div class="gt-slideshow">
      <div class="gt-slide"><img src="images/slide1.jpg" alt="Hoạt động 1"></div>
      <div class="gt-slide"><img src="images/slide2.jpg" alt="Hoạt động 2"></div>
      <div class="gt-slide"><img src="images/slide3.jpg" alt="Hoạt động 3"></div>
      <div class="gt-slide"><img src="images/slide4.jpg" alt="Hoạt động 4"></div>
    </div>
  </div>
</div>

<style>
/* Slideshow – pure CSS fade, 4 slides, 5s each, 20s full cycle */
.gt-slideshow {
  position: relative;
  width: 100%;
  border-radius: 12px;
  overflow: hidden;
  aspect-ratio: 16/9;
  background: #eef6ff;
}
.gt-slide { position: absolute; inset: 0; opacity: 0; transition: opacity .6s ease; }
.gt-slide img { width: 100%; height: 100%; object-fit: cover; display: block; }

/* Keyframes: each slide visible for 25% of timeline */
@keyframes gtFade {
  0% { opacity: 1; }
  20% { opacity: 1; }
  25% { opacity: 0; }
  95% { opacity: 0; }
  100% { opacity: 1; }
}

.gt-slide:nth-child(1) { animation: gtFade 20s infinite; }
.gt-slide:nth-child(2) { animation: gtFade 20s infinite; animation-delay: 5s; }
.gt-slide:nth-child(3) { animation: gtFade 20s infinite; animation-delay: 10s; }
.gt-slide:nth-child(4) { animation: gtFade 20s infinite; animation-delay: 15s; }

@media (max-width: 640px) {
  .gt-slideshow { aspect-ratio: 4/3; }
}

.gt-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 12px;
  margin-bottom: 16px;
}
.stat-card {
  padding: 14px 16px;
  border-radius: 14px;
  color: #fff;
  box-shadow: 0 12px 30px rgba(0,0,0,0.12);
  display: flex;
  justify-content: space-between;
  align-items: center;
  min-height: 120px;
}
.stat-card h2 { margin: 4px 0 0; font-size: 28px; }
.stat-card p { margin: 0; opacity: 0.9; }
.stat-card i { font-size: 28px; opacity: 0.8; }
.stat-card.primary { background: linear-gradient(135deg, #1d4ed8, #22c1dc); }
.stat-card.info { background: linear-gradient(135deg, #0284c7, #38bdf8); }
.stat-card.accent { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
.stat-card.success { background: linear-gradient(135deg, #16a34a, #4ade80); }
.stat-card.danger { background: linear-gradient(135deg, #b91c1c, #f97316); }
.stat-card.teal { background: linear-gradient(135deg, #0f766e, #2dd4bf); }
.stat-card.gold { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.stat-card.neutral { background: linear-gradient(135deg, #475569, #94a3b8); }
.progress-list { display: flex; flex-direction: column; gap: 12px; }
.progress-row { display: grid; grid-template-columns: minmax(0, 2fr) 3fr auto; gap: 12px; align-items: center; }
.progress-label { display: flex; align-items: center; gap: 8px; font-weight: 600; color: #0f172a; }
.progress-track { position: relative; width: 100%; height: 12px; background: #e5e7eb; border-radius: 999px; overflow: hidden; }
.progress-fill { position: absolute; inset: 0; background: linear-gradient(90deg, #22c55e, #0ea5e9); border-radius: inherit; }
.progress-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; min-width: 90px; }
.progress-meta small { color: #6b7280; }
.dot { width: 10px; height: 10px; border-radius: 50%; background: #0ea5e9; display: inline-block; }
@media (max-width: 640px) {
  .progress-row { grid-template-columns: 1fr; }
  .progress-meta { flex-direction: row; justify-content: flex-start; gap: 8px; }
}
</style>

<?php require __DIR__ . '/includes/footer.php'; ?>
