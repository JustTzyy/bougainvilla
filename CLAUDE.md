# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Bougainvilla is a hotel management system built on Laravel 12 (PHP 8.2+). It handles room bookings, guest stays, payments, and staff management with role-based access for Admins, Front Desk operators, and Cleaners.

## Commands

### Development
```bash
composer run dev        # Starts PHP server + queue listener + Vite concurrently
php artisan serve       # PHP dev server only
npm run dev             # Vite dev server only
```

### Testing
```bash
composer test           # Clears config cache, then runs all tests
php artisan test        # Run all PestPHP tests
php artisan test --filter TestName  # Run a single test
```

### Code Quality
```bash
./vendor/bin/pint       # Run Laravel Pint (code style fixer)
```

### Database
```bash
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed
```

## Architecture

### Role System
- `roleID = 1`: Admin — full access via `/adminPages/*` routes
- `roleID = 2`: Front Desk — limited access via `/frontdesk/*` routes
- `roleID = 3`: Cleaner — assigned to stays for room cleanup

Login redirects users to their respective dashboard based on `roleID`. All protected routes use the `auth` middleware.

### Route Structure
Two route prefix groups in [routes/web.php](routes/web.php):
- `adminPages` — managed by controllers in `app/Http/Controllers/` (top-level)
- `frontdesk` — managed by controllers in `app/Http/Controllers/FrontDesk/`

Both groups have parallel `StayController` and `ReportController` implementations with role-appropriate scoping.

### Core Data Model
- **Level** → has many **Rooms**
- **Room** ↔ **Accommodation** (many-to-many via `room_accommodations`)
- **Accommodation** ↔ **Rate** (many-to-many via `rate_accommodations`)
- **Stay** → belongs to Room + Rate; has many **Guests** (via `guest_stays` pivot), **Payments**, and **Receipts**
- **Guest** → linked to stays via `GuestStay`; has an **Address**
- **User** → belongs to **Role**; has an **Address**; has many **Receipts**

Stay statuses: `Standard`, `Extend`, `Cleaning`, `Ready` (defined as constants in `Stay` model).

### Shared Controller Traits
Two traits are mixed into most controllers:
- `EnhancedLoggingTrait` (`app/Http/Controllers/EnhancedLoggingTrait.php`) — provides `logUserAction()`, `logSecurityEvent()`, `logDatabaseOperation()` etc. that write to the custom `logs` table
- `SafeDataAccessTrait` (`app/Http/Controllers/SafeDataAccessTrait.php`) — safe data retrieval helpers

### Soft Deletes
Nearly all models use `SoftDeletes`. Archive pages show soft-deleted records; hard delete is reserved for admin cleanup operations (`CleanupController`).

### Frontend
Blade templates in `resources/views/`, organized into:
- `auth/` — login
- `adminPages/` — admin views
- `frontdeskPages/` — front desk views
- `layouts/` — `admindashboard.blade.php` and `frontdeskdashboard.blade.php` layouts

Styled with Tailwind CSS 4 via Vite (`laravel-vite-plugin`). No separate JS framework — interactions are handled with vanilla JS/Alpine within Blade.

### Rate Limiting
Login endpoint is protected by the custom `rate.limit.login` middleware (`app/Http/Middleware/RateLimitLogin.php`).

### Scheduled Commands
`CleanupGuestsCommand` is registered via `ScheduleServiceProvider` (not the default `routes/console.php` approach).
