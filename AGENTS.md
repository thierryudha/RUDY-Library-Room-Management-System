# AGENTS.md

RUDY — library room reservation system (Laravel 13 standalone REST API + planned Next.js frontend). Only the backend is implemented; treat it as the active codebase.

## Layout
- `backend/` — the real code; run every command from here.
- `frontend/` — empty placeholder (`frontend.txt` only). Next.js not started; do not build here.
- `docs/` — architecture images only (no PRD/ERD/API markdown exists yet, despite README references).

## Commands (run in `backend/`)
- `composer setup` — first-time bootstrap (install, `.env`, key, migrate, vite build).
- `composer dev` — serves app + queue worker + pail logs + vite concurrently.
- `composer test` — `config:clear` then `php artisan test`. Single: `php artisan test --filter=Name`.
- `vendor/bin/pint` — code formatter; run before finishing changes.
- `php artisan db:seed` — roles, master data, demo users, rooms, one sample booking.
- `php artisan storage:link` — required; activation proofs upload to the `public` disk.
- `php artisan schedule:work` — required for the no-show job (`bookings:check-no-show`, scheduled every minute in `routes/console.php`); `composer dev` does NOT start the scheduler.

Tests run on in-memory SQLite regardless of `.env` (`phpunit.xml`); local dev uses PostgreSQL (`DB_CONNECTION=pgsql`, `DB_DATABASE=rudy`). `tests/` currently holds only placeholder `ExampleTest` files.

## Architecture (Service–Repository–Resource)
- Controllers are thin: validate via `app/Http/Requests/Api/**`, call a Service, return a `Resource`.
- Business logic in `app/Services/**`; Eloquent queries in `app/Repositories/**` (extend `BaseRepository`); JSON shaping in `app/Http/Resources/**`.
- All responses use the `ApiResponse` trait envelope `{success, message, data}` via `successResponse()` / `errorResponse()` / `notFoundResponse()` / `validationErrorResponse()`. Never hand-roll JSON in controllers.
- `bootstrap/app.php` already renders global JSON for `api/*` routes (404/401/403/422). Reuse it; don't re-implement exception handling per controller.

## Auth & roles
- Sanctum token auth (`auth:sanctum`) + `role:` route middleware alias (e.g. `role:admin`, `role:admin,super admin`).
- Seeded roles: `super admin`, `admin`, `student`, `lecturer`, `staff`, `alumni`.
- Students register as `pending` and need admin approval before booking (`RegisterService` / `UserManagementService`).

## Data-model conventions (don't break)
- Status columns (`user_status`, `booking_status`, `room_status`) are plain VARCHAR in the DB by design (avoids ENUM ALTERs); enforce values with the PHP backed enums in `app/Enums/**` (`BookingStatus`, `RoomStatus`, `UserStatus`) — never write raw status strings.
- `users` is the central auth table; profile attributes live in one-to-one tables `students` / `lecturers` / `staff` (Class Table Inheritance).
- Pivot tables use composite PKs: `booking_members(booking_id, user_id)`, `room_facilities(room_id, facility_id)`.
- Core entities use SoftDeletes: `users`, `rooms`, `bookings`, `feedbacks`.
- Booking rules (in `BookingService` / `BookingRepository`): max 3h, 1 booking/day for students/alumni, room-overlap check (`isRoomAvailable`), 30-min update/cancel window, penalty after 4 cancellations/no-shows. These are the core product rules — change deliberately.

## Seed users
`admin@rudy.test`/`passwordadmin`, `dosen@rudy.test`/`password`, `mahasiswa@rudy.test`/`password`, `staff@rudy.test`/`passwordstaff`.

## Notes
- `backend/.codegraph/` is the CodeGraph index — call `codegraph_explore` before editing backend code.
- `backend/` planning docs (`base_setup_implementation_plan.md`, `infrastructure_global_configuration_walkthrough.md`, `RUDY-ERD-SQL.sql`) are gitignored; the root README explains the schema rationale.
