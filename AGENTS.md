# AGENTS.md

AKSI KEBAL — employee event-attendance system for Kementerian Agama (Kanwil Kaltara).
Hand-rolled PHP MVC, **no framework**, vanilla CSS/JS. Domain language is Indonesian
throughout: code identifiers, DB columns, and UI.

**Full name:** Absensi Kegiatan Serentak Kementerian Beramal dan Andal
**Version:** 1.3.0 (see `APP_VERSION` in `config/app.php`)
**Brand:** Kementerian Agama Provinsi Kalimantan Utara — warna hijau emerald (`#10b981`).

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
- Default admin login (from seed): NIP `199001012020011001` / password `admin123`.
  **Change immediately in production.**

## Entry point & bootstrap order
`public/index.php` is the single entry point. Load order (must not be changed):
1. `config/app.php` — defines all constants (`BASE_URL`, `APP_NAME`, `APP_ENV`,
   `UPLOAD_PATH`, `MAX_UPLOAD_SIZE`, `ALLOWED_IMAGE_TYPES`, `MAX_COMPRESSED_SIZE`,
   `SESSION_LIFETIME`=3600s) and sets timezone.
2. Session cookie hardening (httponly, SameSite=Strict, strict mode, secure if HTTPS).
3. `session_start()`.
4. Core classes: `Database.php`, `Controller.php`, `Middleware.php`, `helpers.php`.
5. `core/App.php` — instantiates router, dispatches to controller.

## Routing (`core/App.php`) — easy to get wrong
- URL shape `/{controller}/{method}/{param...}` → `{Controller}Controller::{method}($param...)`.
- Controller class = `ucfirst(strtolower(seg0)) . 'Controller'` in `app/controllers/`.
  Default `HomeController::index`; unknown controller/method → `notFound`.
- **Dashes in the method segment become underscores.** URL `admin/pegawai-create` →
  method `pegawai_create()`; `admin/tim-kerja-edit/{slug}` → `tim_kerja_edit($slug)`. So
  methods are snake_case but every link/redirect uses dashes, e.g. `url('admin/pegawai-create')`.
- Remaining segments pass positionally. Query-string routing is also used:
  `absensi?kegiatan={kode}`.

## Controllers (complete method map)

### `AdminController` (`app/controllers/AdminController.php`)
All methods call `Middleware::authAdmin()` first (except `login`, `index`, `logout`).

| URL (dashes) | Method | Description |
|---|---|---|
| `admin/` | `index()` | Redirect to login or dashboard |
| `admin/login` | `login()` | Login page + POST handler |
| `admin/logout` | `logout()` | Destroy session |
| `admin/dashboard` | `dashboard()` | Stats overview |
| `admin/pegawai` | `pegawai()` | Paginated employee list |
| `admin/pegawai-create` | `pegawai_create()` | Create employee |
| `admin/pegawai-edit/{nip}` | `pegawai_edit($nip)` | Edit employee |
| `admin/pegawai-delete/{nip}` | `pegawai_delete($nip)` | Delete employee |
| `admin/tim-kerja` | `tim_kerja()` | Work team list |
| `admin/tim-kerja-create` | `tim_kerja_create()` | Create team |
| `admin/tim-kerja-edit/{slug}` | `tim_kerja_edit($slug)` | Edit team |
| `admin/tim-kerja-delete/{slug}` | `tim_kerja_delete($slug)` | Delete team |
| `admin/unit-kerja` | `unit_kerja()` | Work unit list |
| `admin/unit-kerja-create` | `unit_kerja_create()` | Create unit |
| `admin/unit-kerja-edit/{id}` | `unit_kerja_edit($id)` | Edit unit |
| `admin/unit-kerja-delete/{id}` | `unit_kerja_delete($id)` | Delete unit |
| `admin/jabatan` | `jabatan()` | Position list |
| `admin/jabatan-create` | `jabatan_create()` | Create position |
| `admin/jabatan-edit/{slug}` | `jabatan_edit($slug)` | Edit position |
| `admin/jabatan-delete/{slug}` | `jabatan_delete($slug)` | Delete position |
| `admin/kegiatan` | `kegiatan()` | Event list |
| `admin/kegiatan-resolve-lokasi` | `kegiatan_resolve_lokasi()` | AJAX: geocode lat/lng → address (does NOT consume CSRF) |
| `admin/kegiatan-create` | `kegiatan_create()` | Create event |
| `admin/kegiatan-edit/{kode}` | `kegiatan_edit($kode)` | Edit event |
| `admin/kegiatan-delete/{kode}` | `kegiatan_delete($kode)` | Delete event |
| `admin/kegiatan-publish/{kode}` | `kegiatan_publish($kode)` | Publish event (generates QR code) |
| `admin/kegiatan-qrcode/{identifier}` | `kegiatan_qrcode($identifier)` | View/download QR code page |
| `admin/absensi` | `absensi()` | Attendance list (all events) |
| `admin/absensi-detail/{identifier}` | `absensi_detail($identifier)` | Per-event attendance detail |
| `admin/absensi-export/{identifier}` | `absensi_export($identifier)` | Stream CSV export |
| `admin/absensi-export-pdf/{identifier}` | `absensi_export_pdf($identifier)` | Render HTML print view |
| `admin/absensi-edit/{identifier}` | `absensi_edit($identifier)` | Edit attendance record |
| `admin/absensi-delete/{identifier}` | `absensi_delete($identifier)` | Delete attendance record |
| `admin/log-aktivitas` | `log_aktivitas()` | Audit log viewer |

### `AbsensiController` (`app/controllers/AbsensiController.php`)
Public-facing (no auth guard). Entry point for employees filling attendance via QR link.

| URL | Method | Description |
|---|---|---|
| `absensi?kegiatan={kode}` | `index()` | Attendance form (Published events only) |
| `absensi/get-pegawai-data?nip={nip}` | `getPegawaiData()` | AJAX: return jabatan + tim_kerja for a NIP |
| `absensi/submit` | `submit()` | POST: process attendance submission (validates CSRF) |
| `absensi/sukses/{identifier}` | `sukses($identifier)` | Success confirmation page |

### `HomeController` (`app/controllers/HomeController.php`)
| URL | Method | Description |
|---|---|---|
| `/` | `index()` | Root redirect (to absensi or home page) |
| any unknown | `notFound()` | 404 page |

## Views & layout (`app/views`) — non-obvious
- `Controller::view($path,$data)` only does `extract($data)` + `require app/views/{$path}.php`.
  There is **no automatic layout**.
- Each page view wraps itself: it starts with `<?php ob_start(); ?>`, near the end captures
  optional `$extra_css`/`$extra_js` (nested `ob_start()`/`ob_get_clean()`), sets
  `$content = ob_get_clean();`, then `require_once __DIR__.'/../layouts/main.php'`. The layout
  echoes `$content`/`$extra_css`/`$extra_js`. Copy this pattern for new pages.
- Layouts: `app/views/admin/layouts/main.php` (sidebar highlights via `$active_menu`) and
  `app/views/pegawai/layouts/main.php`.
- Error page: `app/views/errors/404.php` (accepts `$message`).
- After successful absensi: `app/views/pegawai/absensi/sukses.php`.

## Models & DB access
- Models in `app/models/`; class name == file name **including the `Model` suffix** (e.g.
  `KegiatanModel`), loaded via `$this->model('KegiatanModel')`.
- Every model: `$this->db = Database::getInstance();` then the fluent PDO wrapper
  (`core/Database.php`, singleton): `->query($sql)->bind(':x',$v[,PDO::PARAM_INT])->fetch()`
  / `fetchAll()` / `execute()`; also `rowCount()`, `lastInsertId()`,
  `beginTransaction/commit/rollback`, `getStatement()`.
- Always use named placeholders + `bind()`; bind `:limit`/`:offset` with `PDO::PARAM_INT`.

### Model reference
| Model | Key methods |
|---|---|
| `PegawaiModel` | `getAllPaginated`, `countAll`, `findByNip`, `findDetailByNip`, `isNipExists`, `create`, `update`, `delete`, `getAllJabatan`, `getAllTimKerja`, `getAllUnitKerja`, `getListForDropdown` |
| `KegiatanModel` | `getAll`, `getAllPaginated`, `countAll`, `findById`, `findByKode`, `generateKode`, `create`, `update`, `publish`, `delete`, `checkAbsensiRelation` |
| `AbsensiModel` | `getAllPaginated`, `getAllFilteredForExport`, `countAll`, `getStatistik`, `getStatistikLengkap`, `getPegawaiTidakAbsen`, `findById`, `findByKodeAbsensi`, `generateKodeAbsensi`, `create`, `updateStatus`, `delete`, `hasAbsensi`, `getKegiatanList` |
| `DashboardModel` | `getTotalPegawai`, `getTotalKegiatan`, `getTotalKegiatanPublished`, + summary stats |
| `AuthModel` | `findAdminByEmailOrNip`, `findByNip` |
| `LogAktivitasModel` | File-based JSONL reader; paginate, search, filter, auto-cleanup >30d |
| `TimKerjaModel` | CRUD for `tim_kerja` table |
| `UnitKerjaModel` | CRUD for `unit_kerja` table |
| `JabatanModel` | CRUD for `jabatan` table |

## Security conventions (follow for every new endpoint)
- Guard admin actions with `Middleware::authAdmin();` as the first line; the login page uses
  `Middleware::guest();`.
- `Middleware::authAdmin()` also enforces `SESSION_LIFETIME` (1 hour idle timeout).
- Every state-changing POST validates CSRF: `Middleware::validateCsrfToken(input('csrf_token'))`.
  Tokens are **single-use** (consumed on validate). For AJAX that must not burn the token,
  pass `false` as the 2nd arg (see `AdminController::kegiatan_resolve_lokasi`). Emit tokens in
  forms with `csrfField()`.
- Escape all output with `e()`. Hash passwords with `password_hash(..., PASSWORD_DEFAULT)`.
- Session cookies: httponly, SameSite=Strict, strict mode, secure flag on HTTPS — set in
  `public/index.php` before `session_start()`. Do not weaken these.
- Uploads (`AbsensiController::prosesFileBukti` / `scanFileSecurity`) validate extension + real MIME (finfo) + magic bytes + dangerous-content regex + size, then recompress images to JPEG via GD. Reuse this pipeline; never trust the client-supplied extension.
- Upload constants: `MAX_UPLOAD_SIZE`=10 MB (raw), `MAX_COMPRESSED_SIZE`=1 MB (post-compression), `ALLOWED_IMAGE_TYPES`=jpeg/jpg/png.
- **Client-Side Compression**: Untuk menghindari limit *upload* dari *shared hosting* (misal batas 10MB InfinityFree), form *upload* foto dari pegawai **wajib** melakukan *client-side compression* menggunakan HTML5 Canvas (seperti pada `handleFotoUpload`). Izinkan file mentah hingga 25MB di sisi JS, kompres ke JPEG (max 1920px, quality 0.7), ganti objek `File` via `DataTransfer`, baru kemudian dikirim ke server. PDF tidak dikompres lokal (tetap batas 2MB).

## Domain model (DB)
- Tables (Indonesian columns): `pegawai` (PK `nip`, a string), `jabatan`, `tim_kerja`,
  `unit_kerja`, `kegiatan` (PK `id_kegiatan` + short `kode_kegiatan`), `absensi`
  (unique `(nip, id_kegiatan)`).
- Enums matter: `absensi.status_kehadiran` = `'Hadir' | 'Tidak Hadir'`;
  `kegiatan.status_kegiatan` = `'Draft' | 'Published'`. Only `Published` events accept
  attendance and expose a QR code.
- `kegiatan` geolocation fields: `latitude_kegiatan`, `longitude_kegiatan`, `radius_meter`,
  `pakai_lokasi` (tinyint 0/1 — whether geofencing is enforced), `jenis_kegiatan`.
- `absensi` geolocation fields: `latitude_absensi`, `longitude_absensi`, `jarak_meter`,
  `lokasi_valid` (nullable int — distance validation result).
- `absensi` file fields: `foto` (filename in `public/uploads/foto_absensi/`),
  `file_bukti` (filename in `public/uploads/file_bukti/`), `tipe_file_bukti`.
- `absensi` also stores `alasan_tidak_hadir` (text, for Tidak Hadir records).
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
- CSS files: `public/assets/css/admin-layout.css`, `admin-auth.css`, `absensi.css`,
  `pegawai.css`, `home.css`.
- **Alerts & Validations**: Native browser `alert()` is STRICTLY BANNED. All alerts, flash messages, and client-side form validations MUST use `Swal.fire()` (SweetAlert2). Always inject the standard Impeccable CSS classes: `customClass: { popup: 'swal-popup-custom' }` for dialogs and `customClass: { popup: 'swal2-toast-custom' }` for toasts (which must use `toast: true, position: 'top-end'`).

## Helpers (`core/helpers.php`) — full reference
| Function | Signature | Description |
|---|---|---|
| `e()` | `e(?string): string` | XSS-safe `htmlspecialchars` wrapper |
| `redirect()` | `redirect(string $path): void` | Redirect relative to BASE_URL |
| `setFlash()` | `setFlash(string $type, string $msg): void` | Store flash in session |
| `getFlash()` | `getFlash(): ?array` | Consume flash (returns `{type, message}`) |
| `isPost()` / `isGet()` | `(): bool` | Request method checks |
| `input()` | `input(string $key, $default=null)` | Trimmed `$_POST` value |
| `query()` | `query(string $key, $default=null)` | Trimmed `$_GET` value |
| `url()` | `url(string $path=''): string` | Full URL from relative path |
| `asset()` | `asset(string $path): string` | Full URL to `public/assets/` |
| `formatTanggal()` | `formatTanggal(string $date, bool $withDay=true): string` | Indonesian date string |
| `formatWaktu()` | `formatWaktu(string $time): string` | HH:MM format |
| `csrfField()` | `csrfField(): string` | Renders `<input type="hidden" name="csrf_token" ...>` |
| `isAdminLoggedIn()` | `(): bool` | Checks `$_SESSION['admin_logged_in']` |
| `adminData()` | `adminData(?string $key=null)` | Returns admin session data or specific key |
| `truncate()` | `truncate(string $s, int $len=100): string` | Adds ellipsis |
| `generateSlug()` | `generateSlug(string $text): string` | kebab-case, URL-safe slug |

Flash messages are automatically rendered as SweetAlert toasts by the layout if present —
use `setFlash('success'|'error'|'warning'|'info', $msg)` before a redirect.

## Audit Trail & Logging
- Activity logs (audit trail) are written directly to file: `storage/logs/audit-YYYY-MM-DD.log` in JSON Lines (JSONL) format, NOT the database. This prevents database bloat and ensures logs survive even if the actor is deleted.
- Logging is centralized via `$this->logAktivitas('aksi', 'modul', 'deskripsi')` in controllers.
  Full signature: `logAktivitas(string $aksi, string $modul, string $deskripsi, ?string $aktorNip=null, ?string $aktorNama=null)`.
  When `$aktorNip`/`$aktorNama` are null, the method reads from admin session automatically.
- The `LogAktivitasModel` automatically handles reading (newest first), searching, filtering, paginating, and an auto-cleanup process (logs >30 days old are probabilistically deleted).
- Logging is always best-effort (`try/catch`); it will never block the main request if the file system is inaccessible.
- PHP error log (production): `storage/logs/error.log`.

## File storage layout
```
public/uploads/
  foto_absensi/   ← attendance selfie photos (JPEG, recompressed by GD)
  file_bukti/     ← supporting evidence files (image or PDF ≤2 MB)
storage/logs/
  audit-YYYY-MM-DD.log   ← JSONL audit trail
  error.log              ← PHP errors (production only)
```
Upload paths are assembled via the `UPLOAD_PATH` constant (`config/app.php`).

## Base Controller (`core/Controller.php`) — available to all controllers
| Method | Description |
|---|---|
| `$this->model(string $model)` | Instantiate and return a model by class name |
| `$this->view(string $path, array $data=[])` | Extract data and require view file |
| `$this->redirect(string $url)` | Redirect (same as `redirect()` helper) |
| `$this->json($data, int $statusCode=200)` | Send JSON response with correct headers |
| `$this->logAktivitas(...)` | Write audit trail entry (see Logging section) |
| `$this->notFound()` | Render 404 page |

## Development Philosophy
- **Ponytail Rule**: Think like the laziest senior developer. Before writing new code: (1) Check if it's really needed (YAGNI). (2) Reuse existing functions/helpers in the codebase. (3) Use native PHP features. (4) Write the absolute minimum code required to make it work. Tracing and reading the flow is required, but keep solutions minimal and elegant.
