# AGENTS.md

AKSI KEBAL — employee event-attendance system for Kementerian Agama (Kanwil Kaltara).
Hand-rolled PHP MVC, **no framework**, vanilla CSS/JS. Domain language is Indonesian
throughout: code identifiers, DB columns, and UI.

## Tooling reality (read first)
- No Composer, npm, build step, linter, formatter, or tests. There is no
  `composer.json` / `package.json` / `phpunit.xml`; the NextJS + PHPUnit lines in
  `.gitignore` are boilerplate, not real. Do not invent build/test/lint commands.
- Runtime: Apache + `mod_rewrite`, PHP 7.4+ (8.x preferred), MySQL/MariaDB. Required PHP
  extensions: `pdo_mysql` and `gd` (photo compression). Usually run via XAMPP/Laragon.
- Front-end libs (Boxicons, SweetAlert2, Leaflet, QRCode.js) load from CDN — nothing is
  vendored or bundled locally.

## Run & DB setup
- Point Apache docroot at the repo; root `.htaccess` rewrites everything to
  `public/index.php?url=...`. App is served from a **subdirectory**, and `BASE_URL` in
  `config/app.php` is hardcoded to `http://localhost/aksi_kebal_kemenag/public` — edit it
  per environment.
- Create DB `aksi_kebal`, then `mysql -u root -p aksi_kebal < database/aksi_kebal.sql`.
- `database/aksi_kebal.sql` is a full mysqldump (schema + real employee seed; `kegiatan`
  and `absensi` ship empty). `database/migration_*.sql` are historical incremental ALTERs
  already folded into that dump — run them individually only against an older DB.
- DB creds live in `config/database.php` (defaults `root` / empty password).
- `config/app.php` `APP_ENV`: `development` shows errors; `production` hides them and logs
  to `storage/logs/error.log`.

## Routing (`core/App.php`) — easy to get wrong
- URL shape `/{controller}/{method}/{param...}` → `{Controller}Controller::{method}($param...)`.
- Controller class = `ucfirst(strtolower(seg0)) . 'Controller'` in `app/controllers/`.
  Default `HomeController::index`; unknown controller/method → `notFound`.
- **Dashes in the method segment become underscores.** URL `admin/pegawai-create` →
  method `pegawai_create()`; `admin/tim-kerja-edit/{slug}` → `tim_kerja_edit($slug)`. So
  methods are snake_case but every link/redirect uses dashes, e.g. `url('admin/pegawai-create')`.
- Remaining segments pass positionally. Query-string routing is also used:
  `absensi?kegiatan={kode}`.

## Views & layout (`app/views`) — non-obvious
- `Controller::view($path,$data)` only does `extract($data)` + `require app/views/{$path}.php`.
  There is **no automatic layout**.
- Each page view wraps itself: it starts with `<?php ob_start(); ?>`, near the end captures
  optional `$extra_css`/`$extra_js` (nested `ob_start()`/`ob_get_clean()`), sets
  `$content = ob_get_clean();`, then `require_once __DIR__.'/../layouts/main.php'`. The layout
  echoes `$content`/`$extra_css`/`$extra_js`. Copy this pattern for new pages.
- Layouts: `app/views/admin/layouts/main.php` (sidebar highlights via `$active_menu`) and
  `app/views/pegawai/layouts/main.php`.

## Models & DB access
- Models in `app/models/`; class name == file name **including the `Model` suffix** (e.g.
  `KegiatanModel`), loaded via `$this->model('KegiatanModel')`.
- Every model: `$this->db = Database::getInstance();` then the fluent PDO wrapper
  (`core/Database.php`, singleton): `->query($sql)->bind(':x',$v[,PDO::PARAM_INT])->fetch()`
  / `fetchAll()` / `execute()`; also `rowCount()`, `lastInsertId()`,
  `beginTransaction/commit/rollback`, `getStatement()`.
- Always use named placeholders + `bind()`; bind `:limit`/`:offset` with `PDO::PARAM_INT`.

## Security conventions (follow for every new endpoint)
- Guard admin actions with `Middleware::authAdmin();` as the first line; the login page uses
  `Middleware::guest();`.
- Every state-changing POST validates CSRF: `Middleware::validateCsrfToken(input('csrf_token'))`.
  Tokens are **single-use** (consumed on validate). For AJAX that must not burn the token,
  pass `false` as the 2nd arg (see `AdminController::kegiatan_resolve_lokasi`). Emit tokens in
  forms with `csrfField()`.
- Escape all output with `e()`. Hash passwords with `password_hash(..., PASSWORD_DEFAULT)`.
- Uploads (`AbsensiController::prosesFileBukti` / `scanFileSecurity`) validate extension +
  real MIME (finfo) + magic bytes + dangerous-content regex + size, then recompress images to
  JPEG via GD. Reuse this pipeline; never trust the client-supplied extension.

## Domain model (DB)
- Tables (Indonesian columns): `pegawai` (PK `nip`, a string), `jabatan`, `tim_kerja`,
  `unit_kerja`, `kegiatan` (PK `id_kegiatan` + short `kode_kegiatan`), `absensi`
  (unique `(nip, id_kegiatan)`).
- Enums matter: `absensi.status_kehadiran` = `'Hadir' | 'Tidak Hadir'`;
  `kegiatan.status_kegiatan` = `'Draft' | 'Published'`. Only `Published` events accept
  attendance and expose a QR code.
- Public-facing identifiers are short random codes, not numeric ids: `kode_kegiatan`
  (6 chars, `KegiatanModel::generateKode`) and `kode_absensi`. Controllers accept either via
  `ctype_digit($x) ? findById((int)$x) : findByKode($x)` — preserve this dual lookup.
- `jabatan`/`tim_kerja` also carry `slug_*` used in admin URLs (`generateSlug()` helper).

## Timezone (keep both in sync)
- `config/app.php` sets `date_default_timezone_set('Asia/Makassar')` (WITA); `core/Database.php`
  sets the MySQL session `SET time_zone = '+08:00'`. Change them together.

## Reporting
- CSV export streams directly (`AdminController::absensi_export`).
- PDF export is **HTML/CSS print**, not a PDF library: it renders
  `app/views/admin/absensi/pdf_export.php` (Times New Roman, `print-color-adjust`) for the
  browser to print. `app/libraries/fpdf/` is vendored but unused — do not route PDF work
  through FPDF.

## UI / design work
- This repo ships the `impeccable` design skill (`skills-lock.json`, `.agents/skills/impeccable`,
  `.impeccable/`). Use that skill for frontend changes and follow `DESIGN.md` tokens (emerald
  `#10b981`; Poppins headings / Figtree body; defined radii & shadows) plus `PRODUCT.md`.
  DESIGN.md's print/report section has separate rules for the PDF view.
- **Alerts & Validations**: Native browser `alert()` is STRICTLY BANNED. All alerts, flash messages, and client-side form validations MUST use `Swal.fire()` (SweetAlert2). Always inject the standard Impeccable CSS classes: `customClass: { popup: 'swal-popup-custom' }` for dialogs and `customClass: { popup: 'swal2-toast-custom' }` for toasts (which must use `toast: true, position: 'top-end'`).

## Helpers
- Shared functions live in `core/helpers.php`: `url()`, `asset()`, `e()`, `redirect()`,
  `setFlash()/getFlash()` (rendered as a SweetAlert toast in the layout), `input()/query()`,
  `isPost()/isGet()`, `formatTanggal()/formatWaktu()` (Indonesian), `generateSlug()`,
  `csrfField()`, `isAdminLoggedIn()/adminData()`.

## Audit Trail & Logging
- Activity logs (audit trail) are written directly to file: `storage/logs/audit-YYYY-MM-DD.log` in JSON Lines (JSONL) format, NOT the database. This prevents database bloat and ensures logs survive even if the actor is deleted.
- Logging is centralized via `$this->logAktivitas('aksi', 'modul', 'deskripsi')` in controllers.
- The `LogAktivitasModel` automatically handles reading (newest first), searching, filtering, paginating, and an auto-cleanup process (logs >30 days old are probabilistically deleted).
- Logging is always best-effort (`try/catch`); it will never block the main request if the file system is inaccessible.
