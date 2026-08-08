# Setup Arsitektur Basal & Konfigurasi Global API (Laravel 11)

Perencanaan ini bertujuan untuk mempersiapkan pondasi arsitektur "Service-Repository-Resource" pada backend API peminjaman ruangan perpustakaan agar siap digunakan sebelum melanjutkan ke tahap pengembangan fitur/business logic.

## User Review Required

> [!IMPORTANT]
> Harap tinjau rencana ini. Setelah Anda setuju (approve), saya akan mulai mengeksekusi semua perubahan yang tertulis di bawah secara berurutan.

## Open Questions

1. Apakah ada standar penamaan khusus untuk fungsi-fungsi di dalam `BaseRepository` selain CRUD standar (misalnya `findByIdOrFail`, dsb)?
2. Untuk Trait `ApiResponse`, apakah Anda lebih suka format `{"status": "success", "data": {...}}` atau `{"success": true, "data": {...}}`? Pada default rencana ini saya akan menggunakan format `{"success": true/false, "message": "...", "data": {...}}`.

---

## Proposed Changes

### Struktur Folder Kustom & Traits

Setup folder dan Trait yang akan menjadi standar response aplikasi.

#### [NEW] `app/Traits/ApiResponse.php`
- Membuat trait `ApiResponse` yang berisi:
  - `successResponse($data, $message = null, $code = 200)`
  - `errorResponse($message, $code)`
  - `validationErrorResponse($errors)`
  - `notFoundResponse($message = 'Resource not found')`

#### [NEW] Folder Structure
- Membuat/Memastikan folder-folder berikut ada dengan PascalCase:
  - `app/Services` (Wadah logika bisnis)
  - `app/Repositories` (Wadah query Eloquent)
  - `app/Http/Resources` (Presenter layer untuk JSON format)

---

### Reusable Base Repository

Membangun abstraksi agar tidak perlu menulis ulang query CRUD standar di setiap repository.

#### [NEW] `app/Repositories/BaseRepository.php`
- Membuat *abstract class* `BaseRepository` dengan fungsi:
  - `all()`
  - `find($id)`
  - `create(array $data)`
  - `update($id, array $data)`
  - `delete($id)`
  - `paginate($perPage = 15)`
- Menggunakan parameter model generic yang akan di-inject oleh child repository-nya nanti.

---

### REST API Global Configuration (bootstrap/app.php)

Setup Laravel 11 Application bootstrap untuk force JSON dan global exception handling.

#### [MODIFY] `bootstrap/app.php`
- Mengonfigurasi `withExceptions` untuk merender custom JSON ketika terjadi:
  - `NotFoundHttpException` (Route not found / 404)
  - `ModelNotFoundException` (Record not found / 404)
  - `ValidationException` (Validation Error / 422)
  - `AuthenticationException` (Unauthenticated / 401)
  - `AccessDeniedHttpException` / `AuthorizationException` (Forbidden / 403)
- Memastikan Exception tersebut di-format menggunakan fungsi yang ada pada Trait `ApiResponse` atau me-return JSON dengan format standar yang sama.

---

### Autentikasi Sanctum & Custom Role Middleware

#### [NEW] `app/Http/Middleware/EnsureUserHasRole.php`
- Membuat Custom Middleware untuk mengecek role pengguna.
- Jika user belum login, lempar 401 (Unauthenticated).
- Jika role tidak sesuai, lempar 403 (Forbidden) dengan balasan JSON.

#### [MODIFY] `bootstrap/app.php`
- Mendaftarkan alias middleware di dalam blok `withMiddleware`:
  `'role' => \App\Http\Middleware\EnsureUserHasRole::class`
- Memastikan `ForceJsonResponse` diaktifkan secara global untuk request API jika diperlukan (bisa lewat middleware kustom atau `EnsureFrontendRequestsAreStateful`).

#### [MODIFY] `routes/api.php`
- Mengaplikasikan dan memberi contoh penggunaan *wrap* route yang sudah terlindungi oleh `auth:sanctum` dan middleware `role` yang baru saja kita buat.

---

## Verification Plan

### Automated / Syntax Check
- Memastikan tidak ada *syntax error* setelah menjalankan `php artisan optimize:clear`.

### Manual Verification
- Coba memanggil route yang belum terdaftar untuk memicu 404. Harus merespon dengan format JSON standar.
- Coba memanggil route terproteksi tanpa token. Harus merespon dengan format JSON standar 401.
- Coba memanggil route terproteksi dengan token user yang rolenya berbeda. Harus merespon 403 dalam bentuk JSON.
