# DB Schema Check — quanly_doanvien

This file summarizes how the current database structure (as you showed in screenshots) aligns with the code in this project, and proposes focused adjustments for stability, import, and reporting.

Notes:
- Charset: Prefer `utf8mb4` across all tables (you already have it).
- All SQL below is indicative; please review on your MySQL version. If `IF NOT EXISTS` is not supported for `INDEX`, use conditional checks or ignore duplicate-key errors.

## 1) users
- Current: `id (PK AI)`, `username VARCHAR(45)`, `password VARCHAR(45)`, `role VARCHAR(45)`
- Used by: `functions/auth.php` (authenticateUser) — expects those exact columns.
- Status: OK.

## 2) khoa
- Current: `id`, `ten_khoa VARCHAR(45)`, `truong_khoa VARCHAR(45)`, `sdt_lien_he INT`, `email_lien_he VARCHAR(200)`, `mo_ta VARCHAR(45)`
- Used by: import (functions/import_functions.php), listing, joins.
- Mismatch/Advice:
  - `sdt_lien_he` should be `VARCHAR(50)` instead of `INT` to preserve leading zeros and symbols (+84).
  - Consider adding timestamps (optional): `created_at`, `updated_at`.
  - Add index on `ten_khoa` for search/import upsert.
- SQL suggestions:
```
ALTER TABLE khoa MODIFY COLUMN sdt_lien_he VARCHAR(50) NULL;
CREATE INDEX idx_khoa_ten_khoa ON khoa(ten_khoa);
```

## 3) lop
- Current: `id`, `ma_lop VARCHAR(45)`, `ten_lop VARCHAR(45)`, `khoa_id INT`, `co_van VARCHAR(45)`, `bi_thu VARCHAR(45)`
- Used by: code expects columns `co_van`, `bi_thu` (we fixed import to match this).
- Advice:
  - Add index on `ma_lop`.
  - Optional FK: `khoa_id` -> `khoa(id)` ON DELETE SET NULL.
- SQL suggestions:
```
CREATE INDEX idx_lop_ma_lop ON lop(ma_lop);
-- Optional
ALTER TABLE lop ADD CONSTRAINT fk_lop_khoa FOREIGN KEY (khoa_id) REFERENCES khoa(id) ON DELETE SET NULL;
```

## 4) doanvien
- Current: `id`, `ma_sv VARCHAR(50)`, `ho_ten VARCHAR(100)`, `ngay_sinh DATE`, `gioi_tinh VARCHAR(10)`, `sdt VARCHAR(20)`, `email VARCHAR(100)`, `lop_id INT`, `chuc_vu VARCHAR(50)`, `trang_thai VARCHAR(50)`, `ngay_vao_doan DATE`.
- Used by: all modules; import upsert by `ma_sv`.
- Missing/Advice:
  - Add UNIQUE on `ma_sv` to enforce uniqueness of student codes (import relies on this conceptually; code still works without the constraint but DB-level safety is better).
  - Optional FK: `lop_id` -> `lop(id)` ON DELETE SET NULL.
- SQL suggestions:
```
ALTER TABLE doanvien ADD UNIQUE KEY uq_doanvien_ma_sv (ma_sv);
-- Optional
ALTER TABLE doanvien ADD CONSTRAINT fk_doanvien_lop FOREIGN KEY (lop_id) REFERENCES lop(id) ON DELETE SET NULL;
```

## 5) doanphi
- Current: `id`, `doanvien_id INT`, `nam_hoc VARCHAR(20)`, `da_nop TINYINT(1) DEFAULT 0`, `ngay_nop DATE`, `so_tien_nop DECIMAL(12,2)`
- Used by: Đoàn phí pages; year filter uses DISTINCT `nam_hoc` from this table.
- Advice:
  - Add indexes for speed: `INDEX(doanvien_id)`, `INDEX(nam_hoc)`.
  - Optional FK: `doanvien_id` -> `doanvien(id)`.
- SQL suggestions:
```
CREATE INDEX idx_doanphi_dv ON doanphi(doanvien_id);
CREATE INDEX idx_doanphi_nam ON doanphi(nam_hoc);
-- Optional
ALTER TABLE doanphi ADD CONSTRAINT fk_doanphi_dv FOREIGN KEY (doanvien_id) REFERENCES doanvien(id) ON DELETE CASCADE;
```

## 6) danhgia
- Current: `id`, `doanvien_id`, `nam_hoc`, `xep_loai`, `khen_thuong`, `ky_luat`, `ghi_chu`.
- Code: used by `views/danhgia` and `handle/danhgia_process.php`.
- Advice:
  - Add `INDEX(doanvien_id)`, `INDEX(nam_hoc)` for filtering.

## 7) khai_bao, user_declarations
- Current: Screenshots show typical declaration fields.
- Code: there are `khaibao_*` handlers; nothing special required.
- Advice: optional indexes on foreign keys (`doanvien_id`, `user_id`).

## 8) audit_logs
- Expected by code: `id`, `user_id`, `action VARCHAR(50)`, `entity VARCHAR(50)`, `entity_id INT`, `before_json LONGTEXT`, `after_json LONGTEXT`, `created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP`.
- Advice:
  - Ensure `created_at` exists (some older schemas may miss it); add index on `(entity, entity_id)` and `created_at`.
- SQL suggestions:
```
ALTER TABLE audit_logs ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
CREATE INDEX idx_audit_entity ON audit_logs(entity, entity_id);
CREATE INDEX idx_audit_created ON audit_logs(created_at);
```

---

## Import module — required headers vs schema
- Khoa CSV/XLSX headers: `ten_khoa, mo_ta, truong_khoa, sdt_lien_he, email_lien_he` (type-compatible with your schema; `sdt_lien_he` better as VARCHAR).
- Lớp CSV/XLSX headers: `ma_lop, ten_lop, khoa_ten (hoặc khoa_id), co_van, bi_thu` (đây là cột đúng theo DB của bạn; code vẫn hỗ trợ map từ `co_van_hoc_tap`/`bi_thu_chi_doan` nếu gặp file cũ).
- Đoàn viên headers: `ma_sv, ho_ten, ngay_sinh(YYYY-MM-DD), gioi_tinh(Nam/Nữ/Khác), sdt, email, lop_ma (hoặc lop_id), chuc_vu, trang_thai, ngay_vao_doan(YYYY-MM-DD)`.

The importer now commits partial successes: valid rows are saved even if some rows fail. Errors are listed in the result summary.

## Audit logging coverage
- Đoàn viên: CREATE / UPDATE / DELETE — logs before/after; includes `lop_ten` (class name) before and after to show class changes.
- Đoàn phí: CREATE / UPDATE / DELETE — logs after/changes.
- Import: Khoa/Lớp/Đoàn viên and Thu phí import — logs summary with totals and file name.

## Quick checklist
- [ ] Add UNIQUE `doanvien(ma_sv)`
- [ ] Add INDEX `lop(ma_lop)`, `khoa(ten_khoa)`, `doanphi(doanvien_id)`, `doanphi(nam_hoc)`
- [ ] (Optional) Add FKs: `lop.khoa_id`, `doanvien.lop_id`, `doanphi.doanvien_id`
- [ ] (Optional) timestamps on content tables
- [ ] Consider changing `khoa.sdt_lien_he` to `VARCHAR(50)`
