# Cá do Bairro — Comércio Tradicional e de Proximidade [Laravel Refactor]

> A modern Portuguese-language web platform that helps users discover, search, and support **local shops and traditional markets**. Store owners can register their business, manage employees via invite codes, and customers can browse store profiles on an interactive map.

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
- [Visual Identity & Design System](#visual-identity--design-system)
- [Developer Notes & Implementation Rules](#developer-notes--implementation-rules)
- [Planned Features & Backlog](#planned-features--backlog)

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
APP_NAME="Cá do Bairro"
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

## Visual Identity & Design System

The platform's visual identity is built around a warm, tactile Portuguese **"mercado local / mercearia tradicional / carimbo (stamp)"** aesthetic.

### 1. Brand Identity & Purpose
* **Official Name**: **Cá do Bairro** (*"Tudo o que precisa mora cá do bairro"*).
* **Purged Legacy References**: All traces of the legacy name *"S.A.M / C.A.M"* and generic home-service references (e.g. plumbers, electricians) have been excised.
* **Strict Domain**: Dedicated exclusively to neighborhood brick-and-mortar commerce: **mercearias, padarias, pastelarias, talhos, peixarias, drogarias, frutarias e mercados de proximidade**.

### 2. Typography
* **Display / Brand / Headings**: `Bricolage Grotesque` (weights 700/800) with tight tracking (`letter-spacing: -0.02em`) for store names, page titles, and hero headlines.
* **Monospace / Stamps / Data**: `Space Mono` for ticket stubs, category chips, dates, and stamp badges (`.badge-stamp`).
* **Body / Content**: `Inter` and `Nunito` for clear, readable long descriptions, FAQ answers, and forms.
* **Headline Color Split**: Hero titles follow the brand convention: *"Tudo o que precisa,"* with *"Cá do"* in green (`#0f7a42`) and *"Bairro."* in ochre (`#c17f1e`) on its own line.

### 3. Color Palette (CSS Custom Properties)

| Variable | Hex Value | Role & Usage |
|---|---|---|
| `--ink` | `#182b20` | Deep botanical green for body text, navbar background, brand wordmarks, and dark card headers |
| `--paper` | `#f5f2e6` | Warm cream paper tone used as the default canvas background |
| `--paper-2` | `#ede6d2` | Secondary surface background (e.g. input backgrounds, card recesses) |
| `--card` | `#fffdf7` | Crisp warm card surfaces (never cold `#ffffff` or default Bootstrap grey) |
| `--accent-1` | `#0f7a42` | Traditional Portuguese market green for primary actions, badges, and awning stripes |
| `--accent-2` | `#0b5c33` | Deep green shadow and border accent |
| `--ochre` | `#c17f1e` | Terracotta / stamp ochre for badge borders, highlight text, and brand accents |
| `--color-mustard` | `#D9A441` | Warm mustard gold for primary call-to-action buttons (`.btn-pill-mustard`) |

### 4. Component Anatomy & Design Tokens
* **Store Cards (`.store-card`)**: Built with `--card` background, `1px solid rgba(24, 43, 32, 0.1)` border, `16px` rounded corners, and subtle hover lift (`transform: translateY(-3px)`).
* **Market Awning Stripe**: A `6px` top border stripe utilizing a `repeating-linear-gradient(90deg, #0f7a42 0 18px, #c17f1e 18px 36px)` applied to hero and merchant CTA cards.
* **Store Avatars (`.store-detail-avatar`)**: Circular monogram badge (`width: 104px; height: 104px; border-radius: 50%`) with `--ink` background, `4px solid var(--ochre)`, and uppercase 2-letter initials.
* **Market Map Pins (`.custom-map-pin`)**: Green circular DOM badges (`#0f7a42`) with white store icon (`<i class="fa-solid fa-store"></i>`) and white border, replacing confusing numbered pins.
* **Badge Stamps (`.badge-stamp`)**: Angled, dashed-border pill badges replicating authentic market registration stamps.
* **Pill Buttons**: Rounded action buttons (`border-radius: 9999px`) with bold typography.
* **Navbar**: Deep ink background (`#182b20`), centered navigation links, ochre bottom border.
* **Footer**: 3-column Portuguese market layout with ochre top border and Portuguese bio.

---

## Developer Notes & Implementation Rules

Guidelines and conventions discovered during refactoring that all contributors and AI agents must follow:

### 1. Monogram Avatar Initials Generation
* **Filter `[MOCK]` Tags**: Mock data in `MockDataSeeder` is tagged with `[MOCK]`. Always strip `^\[.*?\]\s*` before computing initials.
* **Filter Portuguese Stopwords**: Never include Portuguese articles/prepositions (`de`, `da`, `do`, `dos`, `das`, `e`, `a`, `o`, `&`).
  * *Example*: `[MOCK] Padaria do Bairro Velho` must yield **`PB`** (Padaria + Bairro), not `[P` or `PD`.

### 2. Google Maps JS API Integration
* **Explicit Container Heights**: Mini-map containers must always declare inline `style="height: 260px; min-height: 260px;"` to prevent the container from collapsing to `0px` before the API finishes loading.
* **Modern Bootstrap Loader**: Use Google’s modern asynchronous bootstrap loader with `importLibrary("maps")` and `importLibrary("marker")`, paired with `AdvancedMarkerElement`.
* **API Credentials**: Loaded dynamically via `config('services.google.maps_api_key')` and `config('services.google.map_id')`.

### 3. Layout Stacks & CSS Architecture
* **Stack Targets**: `<x-market-layout>` supports both `@stack('styles')` and `@stack('head')` in `<head>`, and `@stack('scripts')` before `</body>`.
* **Stylesheet Centralization**: Do not leave loose component CSS inside template blocks. Centralize shared component styles in `public/css/meusestilos.css`, which uses automated cache-busting:
  ```html
  <link rel="stylesheet" href="{{ asset('css/meusestilos.css') }}?v={{ filemtime(public_path('css/meusestilos.css')) }}">
  ```

### 4. Database & Feature Realities
* **No Verification Flow**: There is no "store verification" or administrative approval flow in this project. Do not generate fake verification badges or text.
* **Address & Hours in Backlog**: In legacy, `lojas` only stored `lat`/`lon` (the textual address was shown in Places autocomplete but dropped by backend SQL), and opening hours were never structured. Both are scheduled for upcoming updates (see Backlog below).

### 5. Windows / Laragon Environment Quirks
* **PHP Binary**: Located at `C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe`.
* **Git Locks**: Windows DACL permissions on `.git/index.lock` require running Git commits/pushes outside the sandbox (`BypassSandbox: true`).

### 6. Workflow & Approval Protocol
* **Strict Approval Protocol**: Never commit or push to Git without summarizing changes and getting explicit user approval first.
* **Technical Tone**: Keep commit messages technical and accurate (e.g. *"client-side DOM filtering"* instead of marketing buzzwords like *"0ms filtering"*).


---

## Planned Features & Backlog

The following features and improvements were identified during migration and are scheduled for upcoming updates:

### 1. Store Street Address (`morada`)
* **Database Migration**: Add a nullable `address` column to the `stores` table.
* **Store Registration Flow (Phase 6)**: During the Google Places search step in the store registration wizard, extract the full human-readable address (`prediction.description`) from the autocomplete payload and persist it into `stores.address` (resolving the legacy issue where only raw GPS `lat`/`lon` coordinates were retained).
* **UI Integration**: Display the street address prominently on:
  - Store cards on the Explore page (`/explorar`).
  - The Store Detail profile (`/lojas/{id}`) header and Location card.
  - Interactive Map InfoWindows (`/mapa`).

### 2. Store Opening Hours & Schedule (`horario_atendimento`)
* **Schedule Storage**: Introduce a structured opening hours schema (e.g. days of the week, open/close intervals for morning and afternoon shifts, and holiday statuses).
* **Store Dashboard Management**: Enable store managers to edit and update their weekly schedule directly from `/gestao/lojas/{id}`.
* **Dynamic Store Profile Display**: Replace the placeholder *"Horário habitual de bairro"* on `/lojas/{id}` with live open/closed indicators and detailed schedule dropdowns.

### 3. Transactional Email System & Password Reset (Post-Main Merge)
> [!NOTE]
> The modern password reset screens, centralized strong password defaults, and language dictionaries are preserved in Git Stash:
> `password-reset-views-and-backlog`.
> Because email sending and password resets were non-functional placeholders in the legacy app, they were stashed to keep the migration branch focused on 1:1 legacy parity and will be restored after merging into `main`.

* **Restore Stashed Views**: Run `git stash pop` after merging `refactor/laravel-migration` into `main` to restore `resources/views/auth/forgot-password.blade.php`, `resources/views/auth/reset-password.blade.php`, and their associated feature tests.
* **Transactional Mail Driver Configuration**: Configure a real SMTP or API-based mail provider (e.g., Mailgun, Resend, or Postmark) in `.env` to replace the local log driver.
* **Password Reset Delivery**: Enable dispatching of branded HTML reset emails with temporary tokens.
* **Active Contact Form Delivery**: Wire `/contacto` submissions (`InfoController@submitContact`) to a transactional Mailable (`ContactMessageReceived`) that delivers inquiries directly to `suporte@cadobairro.pt`.



