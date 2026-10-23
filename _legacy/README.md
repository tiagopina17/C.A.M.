# S.A.M — Sistema de Apoio ao comércio local (Mercado)

> A Portuguese-language web platform that helps users discover, search, and support **local shops**. Store owners can register their business, manage employees via invite codes, and customers can browse store profiles on an interactive map.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Project Architecture](#project-architecture)
- [Folder & File Map](#folder--file-map)
- [Database Schema](#database-schema)
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

| Layer | Technology |
|---|---|
| **Backend** | PHP 8+ (vanilla, no framework) |
| **Database** | MySQL / MariaDB via **PDO** |
| **Frontend** | HTML5, Bootstrap 5.3, custom CSS |
| **JavaScript** | Vanilla JS, jQuery 3.6, Owl Carousel (explore page) |
| **Icons** | Font Awesome 6, Ionicons 4.5 |
| **Fonts** | Google Fonts — Orbitron (brand), Bricolage Grotesque, Inter, Space Mono, Nunito, Varela Round, Poppins |
| **Maps** | Google Maps JavaScript API + Places API |
| **Server** | Laragon (Apache + MySQL on Windows) |
| **Password Hashing** | Argon2id (fallback: bcrypt) |

---

## Project Architecture

The app follows a **component-include pattern** — each top-level `.php` page is a thin shell that `include_once`'s its shared `<head>`, navbar, content component, and footer:

```
index.php  →  cp_head.php + cp_navbar.php + cp_index_intro.php + cp_footer.php
login.php  →  cp_head.php + cp_navbar.php + cp_login.php        + cp_footer.php
perfil.php →  cp_head.php + cp_navbar.php + cp_perfil.php       + cp_footer.php
...
```

Form submissions POST to **script files** in `scripts/`, which validate, query the database, set session flash messages, and `header("Location: ...")` back.

Some dashboard-style pages (`cp_perfil.php`, `cp_info_lojas.php`, `cp_gerir_loja.php`) include their **own full `<html>` document** (they bypass `cp_head.php` and manage their own `<head>` with Bootstrap/Font Awesome CDN links plus `meusestilos.css`).

---

## Folder & File Map

```
pessoal/                          ← project root (Apache document root)
│
├── .env                          ← Google Maps API key (API_KEY=...) [DO NOT COMMIT]
├── .env.example                  ← Template for environment variables
├── .gitignore                    ← Ignores .env
│
│  ┌──────────── PAGE SHELLS (thin wrappers that include components) ──────────┐
├── index.php                     ← Homepage — hero, store search, feature accordion, CTA
├── login.php                     ← User login form
├── login_funcionario.php         ← Employee/owner login form
├── registo.php                   ← User registration form
├── registo_lojas.php             ← Store registration wizard (2-step: info → Google Places location)
├── perfil.php                    ← User/employee profile dashboard (tabs: info, security, activity)
├── info_lojas.php                ← Employee store management dashboard (list, add, remove stores)
├── gerir_loja.php                ← Single store management (edit info, manage employees, invite codes)
├── loja.php                      ← Public store detail page (info, map embed, services, reviews stub)
├── explorar.php                  ← Browse services by category (Owl Carousel cards)
├── map.php                       ← Full-screen Google Map with all store markers + clustering
├── ajuda.php                     ← FAQ accordion page
├── contacto.php                  ← Contact page (empty — not yet implemented)
├── reset.php                     ← Password reset form (UI only — handler not wired)
├── 403.php                       ← Access denied error page
│  └─────────────────────────────────────────────────────────────────────────────┘
│
├── components/                   ← Reusable PHP component files (HTML + inline PHP)
│   ├── cp_head.php               ← Shared <head>: meta, Bootstrap CSS, custom CSS, fonts, jQuery, Font Awesome
│   ├── cp_navbar.php             ← Main navbar (session-aware: login/register vs. profile dropdown with role-based links)
│   ├── cp_footer.php             ← Footer with social icons, credits, and Bootstrap JS bundle
│   ├── cp_index_intro.php        ← Homepage content: hero section, store search (JS autocomplete), features accordion, CTA
│   ├── cp_login.php              ← Login form UI (email + password, toggle visibility, error display)
│   ├── cp_login_funcionario.php  ← Employee login form UI (same pattern, posts to sc_login_funcionario.php)
│   ├── cp_registo.php            ← Registration form UI (name, email, password, toggle visibility)
│   ├── cp_registo_lojas.php      ← Store registration wizard UI (2-step form, Google Places autocomplete, mock fallback)
│   ├── cp_perfil.php             ← Full profile dashboard (own <html>): edit name/email, change password, stats, "join store" modal, avatar upload stub
│   ├── cp_info_lojas.php         ← Store management dashboard (own <html>): list employee's stores, add/edit/remove via modals
│   ├── cp_gerir_loja.php         ← Single store editor (own <html>): update name/description, employee table, generate invite codes (AJAX), view codes
│   ├── cp_loja.php               ← Public store detail page (own <html>): store header, Google Maps embed, services list, reviews stub
│   ├── cp_explorar.php           ← Browse services by category: fetches categories+services from DB, renders Owl Carousel per category
│   ├── cp_map.php                ← Full-page Google Map: loads stores from DB, renders AdvancedMarkerElement + MarkerClusterer
│   ├── cp_ajuda.php              ← FAQ accordion (Bootstrap collapse)
│   ├── cp_contacto.php           ← Contact page (EMPTY — not yet implemented)
│   ├── cp_reset.php              ← Password reset form UI (email input, posts to sc_reset.php — handler missing)
│   └── cp_403.php                ← 403 error page UI (lock icon, links to home/login/help)
│
├── connections/
│   └── connection.php            ← PDO connection factory: new_db_connection() → mysql:host=localhost;dbname=sam (root, no password)
│
├── scripts/                      ← Server-side form handlers (POST processors, session + redirect)
│   ├── sc_login.php              ← User login: validate → SELECT from utilizadores JOIN tipos → password_verify → set session → redirect by role
│   ├── sc_login_funcionario.php  ← Employee login: validate → SELECT from funcionarios JOIN funcionarios_tipos + funcionarios_lojas → set session → redirect by employee type
│   ├── sc_registo.php            ← User registration: validate (strong password rules) → check duplicate email → INSERT into utilizadores (Argon2id hash) → redirect to login
│   ├── sc_registo_lojas.php      ← Store registration: validate → Google Places API call (get lat/lon from place_id) → INSERT into lojas → migrate user to funcionarios (owner) → DELETE from utilizadores → redirect home
│   ├── sc_join.php               ← Join store via invite code: look up code in funcionarios_codigo → migrate user to funcionarios (employee type 2) → INSERT into funcionarios_lojas → DELETE from utilizadores → consume code → redirect home
│   └── sc_logout.php             ← Clear session, set goodbye flash message, redirect to index
│
├── css/
│   └── meusestilos.css           ← Master stylesheet (~1900 lines): CSS custom properties (brand palette), layout, navbar, buttons, cards, tables, forms, auth pages, hero, search, dashboard, stats, profile, maps, footer, responsive breakpoints
│
├── js/
│   └── scripts.js                ← Navbar shrink on scroll, Bootstrap ScrollSpy, responsive toggler collapse (from StartBootstrap Grayscale)
│
├── images/
│   └── sam.ico                   ← Favicon
│
├── administracao/                ← Admin back-office area (WIP)
│   ├── index.php                 ← Admin entry point: includes admin navbar, head, dashboard, footer
│   ├── components_admin/
│   │   ├── cp_head_admin.php     ← Admin <head> (separate from public site)
│   │   ├── cp_navbar_admin.php   ← Admin navigation bar
│   │   └── cp_index_admin.php    ← Admin dashboard: stats cards (users, services, categories, activity), recent users table, quick actions, category distribution — requires user_tipo_id == 4
│   ├── css_admin/
│   │   └── meus_estilos.css      ← Admin-specific styles
│   └── testes/
│       └── placepicker.php       ← Google Places picker test page
│
└── .idea/                        ← PhpStorm IDE config (committed to repo)
```

---

## Database Schema

Database name: **`sam`** (MySQL, localhost, user `root`, no password)

### Core Tables (inferred from queries)

```
utilizadores                       ← Regular users (customers)
├── id_Utilizadores  (PK)
├── nome             VARCHAR(200)
├── email            VARCHAR(100) UNIQUE
├── password         TEXT          (Argon2id / bcrypt hash)
├── imgperfil        VARCHAR       (profile image filename)
├── ref_id_Tipos     FK → tipos.id_Tipos
└── inicio           DATETIME      (registration date)

tipos                              ← User role types
├── id_Tipos         (PK)
├── nome             VARCHAR       
│   Known values:
│   1 = utilizador (regular user)
│   3 = moderador
│   4 = administrador

funcionarios                       ← Store employees / owners
├── id_Funcionarios  (PK)
├── nome             VARCHAR
├── email            VARCHAR UNIQUE
├── password         TEXT
├── imgperfil        VARCHAR
├── ref_id_Funcionarios_Tipos  FK → funcionarios_tipos
└── inicio           DATETIME

funcionarios_tipos                 ← Employee role types
├── id_Funcionarios_Tipos (PK)
├── nome             VARCHAR
│   Known values:
│   1 = proprietário (owner)
│   2 = funcionário (employee)

lojas                              ← Stores
├── id_Loja          (PK)
├── nome_loja        VARCHAR
├── descricao        TEXT
├── place_id         VARCHAR       (Google Places ID)
├── lat              DECIMAL       (latitude)
├── lon              DECIMAL       (longitude)
├── imgperfil        VARCHAR
└── inicio           DATETIME

funcionarios_lojas                 ← Junction: employee ↔ store (M:N)
├── ref_id_Funcionario  FK → funcionarios.id_Funcionarios
├── ref_id_Loja         FK → lojas.id_Loja
└── inicio              DATETIME

funcionarios_codigo                ← Store invite codes
├── codigo           VARCHAR(7)    (random alphanumeric, unique per store)
└── ref_id_Loja      FK → lojas.id_Loja

utilizadores_lojas                 ← Junction: user ↔ store (for tracking which stores a user follows/visits)
├── ref_id_Utilizador  FK → utilizadores
└── ref_id_Loja        FK → lojas

categorias                         ← Service/product categories
├── id_Categorias    (PK)
└── nome / Nome      VARCHAR

servicos                           ← Services
├── nome             VARCHAR
├── capa             VARCHAR       (cover image filename)
└── ref_id_Categorias FK → categorias

produtos                           ← Products (used on store detail page)
├── nome             VARCHAR
└── ref_id_Categorias FK → categorias
```

### Entity Relationships

```
utilizadores ─── N:1 ──→ tipos
funcionarios ─── N:1 ──→ funcionarios_tipos
funcionarios ─── M:N ──→ lojas         (via funcionarios_lojas)
utilizadores ─── M:N ──→ lojas         (via utilizadores_lojas)
lojas        ─── 1:N ──→ funcionarios_codigo
servicos     ─── N:1 ──→ categorias
produtos     ─── N:1 ──→ categorias
```

---

## Authentication System

### Session Variables (set on login)

| Variable | Description |
|---|---|
| `$_SESSION['user_id']` | PK of user/funcionario |
| `$_SESSION['user_nome']` | Display name |
| `$_SESSION['user_email']` | Email |
| `$_SESSION['user_imgperfil']` | Profile image path |
| `$_SESSION['user_tipo_id']` | Role type ID (from `tipos` or `funcionarios_tipos`) |
| `$_SESSION['user_tipo_nome']` | Role name (e.g., "utilizador", "proprietário") |
| `$_SESSION['logged_in']` | Boolean flag |
| `$_SESSION['user_type']` | `'user'` or `'funcionario'` — key discriminator |
| `$_SESSION['user_loja_id']` | *(funcionario only)* Associated store ID |
| `$_SESSION['user_loja_nome']` | *(funcionario only)* Associated store name |

### Dual Login System

- **Regular users** → `login.php` → `sc_login.php` → queries `utilizadores` table
- **Employees/owners** → `login_funcionario.php` → `sc_login_funcionario.php` → queries `funcionarios` table

### Password Policy (registration)

- Min 8 chars, max 128
- At least 1 uppercase, 1 lowercase, 1 digit, 1 special character
- Cannot contain user's name or email username
- Hashed with Argon2id (memory=65536, time=4, threads=3), bcrypt fallback

### User-to-Employee Migration

When a regular user registers a store (`sc_registo_lojas.php`) or joins via invite code (`sc_join.php`):
1. User data is copied into `funcionarios` table
2. A record is inserted into `funcionarios_lojas` junction table
3. The user is **deleted** from `utilizadores`
4. Session is cleared — user must re-login as `funcionario`

---

## User Roles & Permissions

| Role | `user_type` | `user_tipo_id` | Access |
|---|---|---|---|
| Regular user | `user` | 1 (in `tipos`) | Homepage, explore, store pages, profile, register store, join store |
| Employee | `funcionario` | 2 (in `funcionarios_tipos`) | Same + store management pages |
| Owner | `funcionario` | 1 (in `funcionarios_tipos`) | Same + generate invite codes, manage employees |
| Moderator | `user` | 3 (in `tipos`) | Moderação dashboard link (not implemented) |
| Administrator | `user` | 4 (in `tipos`) | Admin back-office dashboard |

### Access Control

- `cp_perfil.php` — Requires `$_SESSION['user_id']` + `$_SESSION['user_type']`
- `cp_info_lojas.php` — Requires `user_type === 'funcionario'`
- `cp_gerir_loja.php` — Requires `user_type === 'funcionario'` + employee must belong to the store (checked via `funcionarios_lojas` junction)
- `administracao/` — Requires `user_tipo_id === 4`
- `sc_join.php` — Requires `$_SESSION['user_type']` to be set (must be logged in)

---

## Key Features

1. **Homepage with Store Search** — Live autocomplete search that queries all stores client-side (data loaded from PHP into JS array); clicking a result navigates to `loja.php?id=<store_id>`
2. **User Registration / Login** — Separate flows for customers vs. store employees/owners
3. **Store Registration** — 2-step wizard: store name + description → Google Places location search → submits place_id, lat/lon to DB
4. **Employee Invite Codes** — Store owners generate 7-char random codes (stored in `funcionarios_codigo`); users enter the code in their profile to join the store, which migrates them from `utilizadores` to `funcionarios`
5. **Store Management Dashboard** — Employee view of all their stores with add/edit/remove functionality
6. **Single Store Management** — Edit store info, view/manage employees, generate/view invite codes (AJAX)
7. **Public Store Detail** — Display store info, Google Maps embed, services/products list, reviews stub
8. **Interactive Map** — Full-page Google Maps with AdvancedMarkerElement, MarkerClusterer, info windows for all stores
9. **Explore Services** — Browse services grouped by category in Owl Carousel carousels
10. **Profile Management** — Edit name/email, change password, view account stats and activity
11. **Admin Dashboard** — Stats overview (users, services, categories), recent users table, quick actions (WIP — uses slightly different table names like `users`)
12. **Password Reset** — UI exists but handler (`sc_reset.php`) is not yet created

---

## Page Routing

There is **no router** — each page is a physical `.php` file at the document root. Navigation uses standard `<a href="...">` links. Form actions point directly to script files.

| URL | Renders |
|---|---|
| `/index.php` or `/` | Homepage |
| `/login.php` | User login |
| `/login_funcionario.php` | Employee login |
| `/registo.php` | User registration |
| `/registo_lojas.php` | Store registration wizard |
| `/perfil.php` | Profile dashboard |
| `/info_lojas.php` | Store management list |
| `/gerir_loja.php?id=N` | Single store editor |
| `/loja.php?id=N` | Public store detail |
| `/explorar.php` | Browse services |
| `/map.php` | Store map |
| `/ajuda.php` | FAQ |
| `/contacto.php` | Contact (empty) |
| `/reset.php` | Password reset |
| `/403.php` | Access denied |
| `/administracao/` | Admin dashboard |

---

## External Dependencies & APIs

### CDN Libraries

- **Bootstrap 5.3.3** — CSS + JS bundle
- **jQuery 3.6.0** — Used by Owl Carousel and some DOM manipulation
- **Font Awesome 6.x** — Icons
- **Ionicons 4.5.6** — Additional icons (carousel arrows)
- **Google Fonts** — Orbitron, Bricolage Grotesque, Inter, Space Mono, Nunito, Varela Round, Poppins

### Google APIs

- **Google Maps JavaScript API** — Map rendering on `map.php`
- **Google Maps Embed API** — Map embed on `loja.php` (store detail)
- **Google Places API** — Autocomplete for store registration (`registo_lojas.php`)
- **Google Places Details API** — Server-side geocoding in `sc_registo_lojas.php` (cURL call to get lat/lon from place_id)
- **@googlemaps/markerclusterer** — Marker clustering on map page (loaded from unpkg CDN)

### Local JS Libraries (referenced but may not exist in repo)

- `js/jquery.min.js` — Local jQuery copy (used by explore page)
- `js/owl.carousel.min.js` — Owl Carousel plugin
- `js/owl.js` — Owl Carousel init config

---

## Environment Variables

Stored in `.env` at project root (excluded from git):

```env
API_KEY=<Google Maps/Places API key>
```

Loaded by a custom `loadEnv()` function in several files (no Composer autoloader). The function is **duplicated** in `sc_registo_lojas.php`, `cp_loja.php`, `cp_map.php`, and `cp_registo_lojas.php`.

---

## Local Development Setup

### Prerequisites

- **Laragon** (or any Apache + PHP 8+ + MySQL/MariaDB stack)
- PHP extensions: `pdo_mysql`, `curl`, `argon2id` support (PHP 7.2+)
- MySQL database named `sam`

### Steps

1. Clone the repo into `<laragon>/www/pessoal`
2. Create a `.env` file at project root with `API_KEY=<your-google-maps-api-key>`
3. Import the database schema into a MySQL database named `sam` (credentials: `root` / no password / `localhost`)
4. Start Laragon (Apache + MySQL)
5. Visit `http://localhost/pessoal` or `http://pessoal.test` (if using Laragon's pretty URLs)

---

## Design System

The visual identity is a **"local market / carimbo (stamp)"** theme:

### Color Palette (CSS custom properties in `:root`)

| Variable | Value | Usage |
|---|---|---|
| `--ink` | `#182b20` | Primary text, card borders, dark backgrounds |
| `--paper` | `#f5f2e6` | Page backgrounds (cream canvas) |
| `--paper-2` | `#ede6d2` | Card backgrounds |
| `--card` | `#fffdf7` | Near-white card surfaces |
| `--accent-1` | `#0f7a42` | Market green (buttons, accents) |
| `--accent-2` | `#0b5c33` | Deep green (shadows, borders) |
| `--ochre` | `#c17f1e` | Stamp/carimbo ochre (badges, highlights) |
| `--color-teal` | `#00798F` | Secondary accent (brand gradient) |
| `--color-mustard` | `#D9A441` | CTA buttons, navbar registration pill |
| `--color-dark` | `#3B3B3B` | Navbar and footer background |

### Key Visual Elements

- **Badge stamps** (`.badge-stamp`) — Rotated, dashed-border pill badges on each page header
- **Offset box-shadows** — Cards use `4px 4px 0` solid shadows (hand-stamped effect)
- **Scalloped dividers** (`.scallop`) — Awning-style section separators via `radial-gradient`
- **Dashed borders** — Used for info cards, card headers, accordion bodies
- **Pill buttons** — `border-radius: 999px` on all interactive buttons
- **Brand logo** — "S.A.M" rendered in Orbitron with green gradient text fill (`.sam` class)
