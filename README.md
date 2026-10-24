# S.A.M — Sistema de Apoio ao comércio local (Mercado) [Laravel Refactor]

> A modern Portuguese-language web platform that helps users discover, search, and support **local shops**. Store owners can register their business, manage employees via invite codes, and customers can browse store profiles on an interactive map.

---

## Migration Status

This branch (`refactor/laravel-migration`) modernizes S.A.M from procedural vanilla PHP into a robust, normalized **Laravel** application. The original legacy code is fully backed up in `_legacy/` and `_legacy/sam_backup.sql`.

| Phase | Milestone | Status | Notes |
|---|---|---|---|
| **Phase 1** | Safety & Laravel Skeleton | ✅ Completed | PHP 8.3 + Laravel setup in Laragon, Git tracking |
| **Phase 2** | Normalized Database & Eloquent Models | ✅ Completed | `stores`, `store_user`, `categories`, `services`, `service_store`, `store_invite_codes` |
| **Phase 3** | Authentication & Security (Breeze + CSRF) | ✅ Completed | Single unified auth for users & employees, brute-force protection, CSRF |
| **Phase 4** | Design & Asset Migration | ⏳ Up Next | Port `meusestilos.css`, Blade layouts, and reusable components |
| **Phase 5** | Public Pages | ⏹️ Pending | Home with live store search, Explore, Interactive Map, Store Detail |
| **Phase 6** | Store Management & Invite Codes | ⏹️ Pending | Store wizard, employee management, 7-char code generator |
| **Phase 7** | Admin Backoffice & Data Importer | ⏹️ Pending | Admin dashboard + legacy migration script from `sam` to `sam_laravel` |
| **Phase 8** | QA & Verification | ⏹️ Pending | Automated feature test suite + final cleanup |

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Project Architecture](#project-architecture)
- [Folder & File Map](#folder--file-map)
- [Database Schema & Relationships](#database-schema--relationships)
- [Authentication System](#authentication-system)
- [User Roles & Permissions](#user-roles--permissions)
- [Key Features](#key-features)
- [Page Routing](#page-routing)
- [External Dependencies & APIs](#external-dependencies--apis)
- [Environment Variables](#environment-variables)
- [Local Development Setup](#local-development-setup)
- [Design System](#design-system)

---

## Tech Stack

| Layer | Technology | Description |
|---|---|---|
| **Backend** | PHP 8.3 + Laravel | Modern MVC architecture with Eloquent ORM |
| **Database** | MySQL 8.4 via PDO | Relational database (`sam_laravel`) hosted in Laragon |
| **Testing** | PHPUnit 12 | In-memory SQLite feature and model testing suite |
| **Frontend** | Blade + Bootstrap 5.3 + custom CSS | Component-based templating with unified styling |
| **JavaScript** | Vanilla JS / ES Modules | Lightweight client-side scripts, Google Maps loader |
| **Icons** | Font Awesome 6, Ionicons | Comprehensive iconography |
| **Fonts** | Google Fonts | Orbitron (brand), Bricolage Grotesque, Inter, Space Mono |
| **Maps** | Google Maps JavaScript API + Places API | Store geolocation, marker clustering, address autocomplete |
| **Environment** | Laragon (Windows) | Blazing-fast native PHP 8.3 & MySQL development environment |

---

## Project Architecture

Unlike the legacy application (which used procedural file-includes and direct database queries in scripts), this refactor strictly follows Laravel's **MVC (Model-View-Controller)** pattern:

```
Request (e.g. GET /lojas/1)
   │
   ▼
routes/web.php (Route Matching & Middleware)
   │
   ▼
app/Http/Controllers/StoreController.php (Business Logic)
   │
   ├── Uses Eloquent Models (app/Models/Store.php) ──► MySQL (sam_laravel)
   │
   ▼
resources/views/stores/show.blade.php (Blade Template + Layouts)
   │
   ▼
HTML Response with CSRF Protection & Escaped Variables
```

* **Security**: Built-in CSRF verification on every form (`@csrf`), Argon2id/Bcrypt password hashing, SQL injection prevention via Eloquent PDO binding, and automated XSS protection via Blade `{{ }}` syntax.
* **Unified Users**: Customers and employees share the same `users` table; store affiliations and permissions are governed through the `store_user` pivot relationship.

---

## Folder & File Map

```
pessoal/
├── app/
│   ├── Http/
│   │   ├── Controllers/            ← Route controllers (StoreController, etc.)
│   │   └── Middleware/             ← Custom route & role access middleware
│   └── Models/                     ← Eloquent Models (with relationships)
│       ├── User.php                ← stores(), ownedStores(), employedStores()
│       ├── Store.php               ← users(), owners(), employees(), services(), inviteCodes()
│       ├── Category.php            ← services()
│       ├── Service.php             ← category(), stores()
│       └── StoreInviteCode.php     ← store(), user(), isValid()
│
├── bootstrap/                      ← Application lifecycle and service providers
├── config/                         ← Configuration files (database, auth, app)
│
├── database/
│   ├── factories/                  ← Model factories for testing and seeding
│   │   ├── StoreFactory.php
│   │   ├── CategoryFactory.php
│   │   ├── ServiceFactory.php
│   │   └── StoreInviteCodeFactory.php
│   ├── migrations/                 ← Version-controlled database schema migrations
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_10_23_223557_create_stores_table.php
│   │   ├── 2026_10_23_224056_create_store_user_table.php
│   │   ├── 2026_10_23_224336_create_categories_table.php
│   │   ├── 2026_10_23_224346_create_services_table.php
│   │   ├── 2026_10_23_224358_create_service_store_table.php
│   │   └── 2026_10_23_224408_create_store_invite_codes_table.php
│   └── seeders/                    ← Database seeders (dev data and legacy importers)
│
├── public/                         ← Document root exposed to the web server
│   ├── css/
│   │   └── meusestilos.css         ← Master theme stylesheet (~1900 lines)
│   ├── js/                         ← Client-side scripts
│   └── index.php                   ← Laravel application entry point
│
├── resources/
│   └── views/                      ← Blade templates
│       ├── components/             ← Reusable components (<x-navbar>, <x-footer>)
│       ├── layouts/                ← Base HTML layouts (app.blade.php)
│       ├── stores/                 ← Store views (show, index, create, edit)
│       └── home.blade.php          ← Homepage with hero & search
│
├── routes/
│   ├── web.php                     ← Application web routes
│   └── console.php                 ← Artisan CLI commands
│
├── tests/
│   └── Feature/                    ← PHPUnit integration test suite
│       └── StoreModelTest.php      ← Automated test verifying models & relationships
│
├── _legacy/                        ← SAFE ARCHIVE of all original procedural PHP files
│   ├── sam_backup.sql              ← Complete SQL dump of original `sam` database
│   └── README.md                   ← Original legacy architecture documentation
│
├── .env                            ← Local environment configuration [DO NOT COMMIT]
├── .env.example                    ← Template for environment variables
├── composer.json                   ← PHP dependencies and autoloader mappings
└── MIGRATION.md                    ← Step-by-step migration roadmap and progress checklist
```

---

## Database Schema & Relationships

Database: **`sam_laravel`** (MySQL 8.4)

### Entity Relationship Diagram

```
 users ───────( M:N via store_user )───────► stores ◄──────( 1:N )─────── store_invite_codes
                                                │
                                                ▼ ( M:N via service_store )
                                             services
                                                │
                                                ▼ ( N:1 )
                                            categories
```

### Table Definitions

#### `users`
* `id`: PK (BigInt, Auto-Increment)
* `name`: User's full name (VARCHAR)
* `email`: Unique email address (VARCHAR)
* `password`: Secure hashed password (Argon2id / Bcrypt)
* `remember_token`: Session persistence token
* `timestamps`: `created_at`, `updated_at`

#### `stores`
* `id`: PK (BigInt, Auto-Increment)
* `name`: Store name (VARCHAR)
* `description`: About the store (TEXT, nullable)
* `place_id`: Google Places identifier (VARCHAR, nullable)
* `lat`: Decimal latitude coordinate `(10, 7)` (nullable)
* `lon`: Decimal longitude coordinate `(10, 7)` (nullable)
* `logo_path`: Store profile image filename or path (VARCHAR, nullable)
* `timestamps`: `created_at`, `updated_at`

#### `store_user` (Pivot Table)
* `id`: PK
* `store_id`: FK &rarr; `stores.id` (onDelete: CASCADE)
* `user_id`: FK &rarr; `users.id` (onDelete: CASCADE)
* `role`: `'owner'` or `'employee'` (default: `'employee'`)
* `timestamps`: `created_at`, `updated_at`
* `UNIQUE(store_id, user_id)`: Prevents duplicate associations

#### `categories`
* `id`: PK
* `name`: Category display name (e.g., "Padaria & Pastelaria")
* `slug`: Unique URL-friendly slug (e.g., `padaria-pastelaria`)
* `description`: Category summary (TEXT, nullable)
* `icon`: FontAwesome/Ionicons icon class (nullable)
* `timestamps`: `created_at`, `updated_at`

#### `services`
* `id`: PK
* `category_id`: FK &rarr; `categories.id` (onDelete: CASCADE)
* `name`: Service or product name
* `slug`: URL-friendly identifier
* `image_path`: Cover image path (nullable)
* `timestamps`: `created_at`, `updated_at`

#### `service_store` (Pivot Table)
* `id`: PK
* `store_id`: FK &rarr; `stores.id` (onDelete: CASCADE)
* `service_id`: FK &rarr; `services.id` (onDelete: CASCADE)
* `timestamps`: `created_at`, `updated_at`
* `UNIQUE(store_id, service_id)`

#### `store_invite_codes`
* `id`: PK
* `store_id`: FK &rarr; `stores.id` (onDelete: CASCADE)
* `code`: Unique 7–10 character random alphanumeric string
* `expires_at`: Expiration timestamp (nullable)
* `used_by`: FK &rarr; `users.id` (nullable, on nullify: SET NULL)
* `used_at`: Timestamp when code was claimed (nullable)
* `timestamps`: `created_at`, `updated_at`

---

## Authentication System

### Legacy vs. Laravel Modernization

| Feature | Legacy Procedural (`_legacy/`) | Modern Laravel Refactor |
|---|---|---|
| **Login Entry Points** | Separate `login.php` and `login_funcionario.php` | Single unified `/login` route |
| **User Migration Bug** | Legacy deleted users from `utilizadores` to add them to `funcionarios` | Single `users` table; permissions linked via `store_user` |
| **CSRF Protection** | None (vulnerable to Cross-Site Request Forgery) | Built-in token verification (`@csrf`) on all POST/PUT/DELETE forms |
| **Brute Force Protection** | None | Built-in IP & email rate-limiting via Laravel Breeze |
| **Password Hashing** | Custom Argon2id/Bcrypt script calls | Automatic secure hashing with verification via `Hash::make()` |
| **Session Tracking** | Manual `$_SESSION` global array | Secure, encrypted database or cookie session driver |

---

## User Roles & Permissions

Instead of dividing people into disconnected tables, access is determined dynamically:

1. **Customer (Regular User)**:
   * Has a record in `users`.
   * Can browse public stores, view the map, explore services, and maintain their profile.
   * Can register a new store (which assigns them the `'owner'` role for that store) or enter an invite code (which links them as an `'employee'`).

2. **Store Employee**:
   * Has an active record in `store_user` where `role = 'employee'`.
   * **Business Rule**: An employee can only be actively assigned to **one store** at a time.
   * Has access to their assigned store's internal dashboard.

3. **Store Owner**:
   * Has records in `store_user` where `role = 'owner'`.
   * Can own and manage multiple stores.
   * Can edit store information, view/remove employees, and generate 7-character invite codes.

4. **Administrator**:
   * Has elevated system-wide access to manage all categories, services, stores, and users.

---

## Key Features

1. **Live Store Search** — Autocomplete search bar on the homepage allowing customers to quickly jump to any store profile.
2. **Interactive Map (`/mapa`)** — Google Maps JavaScript API with AdvancedMarkerElement and clustering to locate shops by neighborhood.
3. **Category Explorer (`/explorar`)** — Filter and discover local services and products organized cleanly by category.
4. **2-Step Store Registration** — Streamlined store creation with Google Places autocomplete for accurate latitude, longitude, and address capture.
5. **Staff Management via Invite Codes** — Owners generate secure, expiring 7-character codes; prospective staff enter the code in their profile to join the store without complex onboarding.
6. **Store Profiles (`/lojas/{id}`)** — Public profiles showing location map, contact info, catalog, and store reviews.

---

## Environment Variables

Stored in `.env` at the project root (never committed to version control):

```env
APP_NAME=SAM
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://pessoal.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sam_laravel
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_MAPS_API_KEY=<your-google-maps-api-key>
```

---

## Local Development Setup

### Prerequisites
* **Laragon** (Windows) with PHP 8.3+ and MySQL 8.4+
* **Composer 2.8+**

### Quick Start
1. Clone the repository and switch to the refactor branch:
   ```bash
   git checkout refactor/laravel-migration
   ```
2. Ensure dependencies are installed:
   ```bash
   composer install
   ```
3. Set up your `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Start MySQL in Laragon (click **"Start All"** in Laragon).
5. Run database migrations:
   ```bash
   php artisan migrate
   ```
6. Run the automated test suite to verify everything:
   ```bash
   php artisan test
   ```
7. Visit the site in your browser at `http://localhost/pessoal/public` or `http://pessoal.test`.

---

## Design System

The visual identity preserves the Portuguese **"local market / carimbo (stamp)"** aesthetic:

### Color Palette (CSS Custom Properties)

| Variable | Value | Role |
|---|---|---|
| `--ink` | `#182b20` | Primary dark green ink for text, borders, headers |
| `--paper` | `#f5f2e6` | Cream paper background canvas |
| `--paper-2` | `#ede6d2` | Secondary surface background |
| `--card` | `#fffdf7` | Crisp warm card surfaces |
| `--accent-1` | `#0f7a42` | Market green (primary buttons, success accents) |
| `--accent-2` | `#0b5c33` | Deep green shadow and border accent |
| `--ochre` | `#c17f1e` | Stamp/carimbo warm ochre for badges and highlights |
| `--color-mustard` | `#D9A441` | Accent buttons & call-to-actions |

### Visual Components
* **Badge Stamps (`.badge-stamp`)**: Angled, dashed-border pill badges replicating authentic market stamps.
* **Offset Box-Shadows**: `4px 4px 0px` solid shadows giving an artisan, tactile feel.
* **Scalloped Dividers (`.scallop`)**: Awning-inspired section dividers.
* **Pill Buttons**: Full-rounded action buttons (`border-radius: 999px`).

### Table Definitions

#### `users`
* `id`: PK (BigInt, Auto-Increment)
* `name`: User's full name (VARCHAR)
* `email`: Unique email address (VARCHAR)
* `password`: Secure hashed password (Argon2id / Bcrypt)
* `remember_token`: Session persistence token
* `timestamps`: `created_at`, `updated_at`

#### `stores`
* `id`: PK (BigInt, Auto-Increment)
* `name`: Store name (VARCHAR)
* `description`: About the store (TEXT, nullable)
* `place_id`: Google Places identifier (VARCHAR, nullable)
* `lat`: Decimal latitude coordinate `(10, 7)` (nullable)
* `lon`: Decimal longitude coordinate `(10, 7)` (nullable)
* `logo_path`: Store profile image filename or path (VARCHAR, nullable)
* `timestamps`: `created_at`, `updated_at`

#### `store_user` (Pivot Table)
* `id`: PK
* `store_id`: FK &rarr; `stores.id` (onDelete: CASCADE)
* `user_id`: FK &rarr; `users.id` (onDelete: CASCADE)
* `role`: `'owner'` or `'employee'` (default: `'employee'`)
* `timestamps`: `created_at`, `updated_at`
* `UNIQUE(store_id, user_id)`: Prevents duplicate associations

#### `categories`
* `id`: PK
* `name`: Category display name (e.g., "Padaria & Pastelaria")
* `slug`: Unique URL-friendly slug (e.g., `padaria-pastelaria`)
* `description`: Category summary (TEXT, nullable)
* `icon`: FontAwesome/Ionicons icon class (nullable)
* `timestamps`: `created_at`, `updated_at`

#### `services`
* `id`: PK
* `category_id`: FK &rarr; `categories.id` (onDelete: CASCADE)
* `name`: Service or product name
* `slug`: URL-friendly identifier
* `image_path`: Cover image path (nullable)
* `timestamps`: `created_at`, `updated_at`

#### `service_store` (Pivot Table)
* `id`: PK
* `store_id`: FK &rarr; `stores.id` (onDelete: CASCADE)
* `service_id`: FK &rarr; `services.id` (onDelete: CASCADE)
* `timestamps`: `created_at`, `updated_at`
* `UNIQUE(store_id, service_id)`

#### `store_invite_codes`
* `id`: PK
* `store_id`: FK &rarr; `stores.id` (onDelete: CASCADE)
* `code`: Unique 7–10 character random alphanumeric string
* `expires_at`: Expiration timestamp (nullable)
* `used_by`: FK &rarr; `users.id` (nullable, on nullify: SET NULL)
* `used_at`: Timestamp when code was claimed (nullable)
* `timestamps`: `created_at`, `updated_at`

---

## Authentication System

### Legacy vs. Laravel Modernization

| Feature | Legacy Procedural (`_legacy/`) | Modern Laravel Refactor |
|---|---|---|
| **Login Entry Points** | Separate `login.php` and `login_funcionario.php` | Single unified `/login` route |
| **User Migration Bug** | Legacy deleted users from `utilizadores` to add them to `funcionarios` | Single `users` table; permissions linked via `store_user` |
| **CSRF Protection** | None (vulnerable to Cross-Site Request Forgery) | Built-in token verification (`@csrf`) on all POST/PUT/DELETE forms |
| **Brute Force Protection** | None | Built-in IP & email rate-limiting via Laravel Breeze |
| **Password Hashing** | Custom Argon2id/Bcrypt script calls | Automatic secure hashing with verification via `Hash::make()` |
| **Session Tracking** | Manual `$_SESSION` global array | Secure, encrypted database or cookie session driver |

---

## User Roles & Permissions

Instead of dividing people into disconnected tables, access is determined dynamically:

1. **Customer (Regular User)**:
   * Has a record in `users`.
   * Can browse public stores, view the map, explore services, and maintain their profile.
   * Can register a new store (which assigns them the `'owner'` role for that store) or enter an invite code (which links them as an `'employee'`).

2. **Store Employee**:
   * Has an active record in `store_user` where `role = 'employee'`.
   * **Business Rule**: An employee can only be actively assigned to **one store** at a time.
   * Has access to their assigned store's internal dashboard.

3. **Store Owner**:
   * Has records in `store_user` where `role = 'owner'`.
   * Can own and manage multiple stores.
   * Can edit store information, view/remove employees, and generate 7-character invite codes.

4. **Administrator**:
   * Has elevated system-wide access to manage all categories, services, stores, and users.

---

## Key Features

1. **Live Store Search** — Autocomplete search bar on the homepage allowing customers to quickly jump to any store profile.
2. **Interactive Map (`/mapa`)** — Google Maps JavaScript API with AdvancedMarkerElement and clustering to locate shops by neighborhood.
3. **Category Explorer (`/explorar`)** — Filter and discover local services and products organized cleanly by category.
4. **2-Step Store Registration** — Streamlined store creation with Google Places autocomplete for accurate latitude, longitude, and address capture.
5. **Staff Management via Invite Codes** — Owners generate secure, expiring 7-character codes; prospective staff enter the code in their profile to join the store without complex onboarding.
6. **Store Profiles (`/lojas/{id}`)** — Public profiles showing location map, contact info, catalog, and store reviews.

---

## Environment Variables

Stored in `.env` at the project root (never committed to version control):

```env
APP_NAME=SAM
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://pessoal.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sam_laravel
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_MAPS_API_KEY=<your-google-maps-api-key>
```

---

## Local Development Setup

### Prerequisites
* **Laragon** (Windows) with PHP 8.3+ and MySQL 8.4+
* **Composer 2.8+**

### Quick Start
1. Clone the repository and switch to the refactor branch:
   ```bash
   git checkout refactor/laravel-migration
   ```
2. Ensure dependencies are installed:
   ```bash
   composer install
   ```
3. Set up your `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Start MySQL in Laragon (click **"Start All"** in Laragon).
5. Run database migrations:
   ```bash
   php artisan migrate
   ```
6. Run the automated test suite to verify everything:
   ```bash
   php artisan test
   ```
7. Visit the site in your browser at `http://localhost/pessoal/public` or `http://pessoal.test`.

---

## Design System

The visual identity preserves the Portuguese **"local market / carimbo (stamp)"** aesthetic:

### Color Palette (CSS Custom Properties)

| Variable | Value | Role |
|---|---|---|
| `--ink` | `#182b20` | Primary dark green ink for text, borders, headers |
| `--paper` | `#f5f2e6` | Cream paper background canvas |
| `--paper-2` | `#ede6d2` | Secondary surface background |
| `--card` | `#fffdf7` | Crisp warm card surfaces |
| `--accent-1` | `#0f7a42` | Market green (primary buttons, success accents) |
| `--accent-2` | `#0b5c33` | Deep green shadow and border accent |
| `--ochre` | `#c17f1e` | Stamp/carimbo warm ochre for badges and highlights |
| `--color-mustard` | `#D9A441` | Accent buttons & call-to-actions |

### Visual Components
* **Badge Stamps (`.badge-stamp`)**: Angled, dashed-border pill badges replicating authentic market stamps.
* **Offset Box-Shadows**: `4px 4px 0px` solid shadows giving an artisan, tactile feel.
* **Scalloped Dividers (`.scallop`)**: Awning-inspired section dividers.
* **Pill Buttons**: Full-rounded action buttons (`border-radius: 999px`).

