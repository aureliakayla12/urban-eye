# PRD – UrbanEye: Urban Environmental Issue Reporting & Monitoring System

## 1. Product Overview

UrbanEye is a **web-based crowdsourcing platform** that connects citizens and local government agencies to **report, verify, assign, and resolve** urban environmental issues (garbage, damaged roads, broken streetlights, clogged drainage, etc.) in Depok City.

This system is **not intended to be a full municipal service desk / SIPD replacement**. Its primary focus is:
- Citizen-submitted issue reporting (with photo, AI-assisted categorization, and geolocation)
- Verification and field-officer assignment workflow
- Transparent status tracking with a full audit trail
- Points, badges, and rewards to drive sustained citizen participation

This PRD is written directly from the **already-created Laravel migrations** (see `/database/migrations`), so the schema below is the actual source of truth — not a proposal.

---

## 2. Objectives

### Goals
- Let citizens submit environmental reports with photo(s), location, and category in under 5 steps
- Automatically suggest a report category from the photo using an AI vision API
- Let admins verify reports (valid/hoax) and assign them to field officers
- Let field officers accept, resolve, and upload proof of handling
- Maintain a transparent, immutable status-change history per report
- Reward citizen participation via points, badges, and a redeemable rewards catalog
- Support role-based access control (Admin, Petugas/Officer, Masyarakat/Citizen) via Spatie Permission

### Non-Goals
- Automatic/real government-system integration (SIPD, etc.)
- Automatic reward fulfillment with third-party vendors (payment gateway, e-voucher API)
- Native mobile app (web app only)
- Multi-city support (Depok only for this phase)
- Push/email/SMS notifications (in-app notifications only)

---

## 3. Definitions

| Term | Definition |
|----|----|
| Report | A citizen-submitted record of an environmental issue, tied to a category, location, and photo(s) |
| Verification Status | Admin's judgment on whether a report is genuine (`pending`, `valid`, `hoax`) |
| Report Status | The overall lifecycle stage of a report (`menunggu`, `diproses`, `selesai`, `ditolak`) |
| Assignment | The act of an admin assigning a report to a specific field officer (`report_assignments`) |
| Status History | An immutable log entry recording every change in a report's status (`report_status_histories`) |
| Points | Reward currency earned/deducted per report outcome, stored per-transaction in `user_points` and aggregated in `users.total_points` |
| Badge | An achievement granted to a user once a points/report threshold is met |
| Reward | A catalog item redeemable using accumulated points |

---

## 4. Tech Stack & Architecture

### Backend
- Laravel
- MySQL
- Spatie Laravel-Permission (Authentication & Authorization — roles: `admin`, `petugas`, `masyarakat`)
- Google Gemini API (AI-assisted photo category classification)
- Seeders:
  - Users, Roles and Permissions (`admin`, `petugas`, `masyarakat`)
  - Categories (report categories: Sampah, Jalan Rusak, Lampu Mati, Drainase, Lainnya)
  - Districts & Villages (Depok administrative regions)
  - Badges (achievement thresholds)
  - Rewards (initial catalog: voucher, pulsa, bibit tanaman, lainnya)

### Frontend
- Inertia.js
- Vue.js 3
- Tailwind CSS + DaisyUI
- Leaflet.js (interactive map, location picking)
- Component-based architecture per module

### Development Environment
- Laragon
- Git & GitHub (version control + CI deploy)
- Railway (deployment)

### Architecture Pattern
- MVC (Laravel)
- Service Layer for: AI classification calls, points calculation, status-transition validation
- Policy-based authorization (Spatie) per role

---

## 5. User Roles

| Role | Description |
|----|----|
| Admin | Full access: verify reports, assign officers, manage users/roles, manage master data (categories, districts, villages, badges, rewards), view statistics |
| Petugas (Field Officer) | Accepts assigned reports, updates handling status, uploads proof photo + notes |
| Masyarakat (Citizen) | Creates reports, tracks own report history, comments, earns points/badges, redeems rewards, views leaderboard |

---

## 6. Database Scope

⚠️ **IMPORTANT**
All tables listed below already exist as Laravel migrations and **MUST NOT BE MODIFIED** without updating the migration files first.
Application logic must adapt strictly to these structures.

### Tables Used
- users
- password_reset_tokens, sessions *(Laravel defaults, not business-relevant)*
- roles, permissions, model_has_roles, model_has_permissions, role_has_permissions *(Spatie Permission)*
- categories
- districts
- villages
- reports
- report_images
- report_assignments
- report_status_histories
- comments
- notifications
- user_points
- badges
- user_badges
- rewards
- reward_redemptions

---

## 7. Module Requirement / Menu in Sidebar

### 7.1 User & Role Management (Admin)

- Admin can create new users directly (no public self-service admin/officer creation)
- Create Role, Create Permission (via Spatie)
- Attach Role to User, Attach Permission to Role
- Citizens self-register with default role `masyarakat`
- `total_points` on `users` is a **denormalized running total** — always derived from the sum of `user_points`, never edited directly

---

### 7.2 Master Data Management (Admin)

- **Categories**: name, description, icon — used to classify reports (e.g. Sampah, Jalan Rusak)
- **Districts / Villages**: two-level location hierarchy (`villages.district_id → districts.id`), used to tag and filter reports by administrative area
- **Badges**: name, description, icon, `required_points`, `required_reports` — thresholds used for automatic badge awarding
- **Rewards**: name, `reward_type` (`voucher`/`pulsa`/`bibit`/`lainnya`), `point_cost`, `stock`, `status` (active/inactive) — the redeemable catalog

---

### 7.3 Report Management (Masyarakat)

- Citizen creates a new report with:
  - `title`, `description`, `category_id`
  - primary `photo` + optional additional images in `report_images` (photo gallery)
  - `latitude`/`longitude` (auto GPS or manual pin) + free-text `address`
  - optional `district_id` / `village_id` (auto-derived from location or manually selected)
- On upload, the system sends the primary photo to the **Google Gemini API**; response is stored in `ai_category`, `ai_confidence`, `ai_response`, and `classified_at` on the `reports` row
  - `ai_category` is a **suggestion only** — the citizen's chosen `category_id` remains authoritative
- New report is created with:
  - `verification_status = pending`
  - `status = menunggu`
- Citizen can add `comments` to their own or other public reports (community discussion / cross-verification)
- Citizen views own report history with full `report_status_histories` timeline

---

### 7.4 Report Verification (Admin)

- Admin reviews reports where `verification_status = pending`
- Admin sets `verification_status`:
  - `valid` → report proceeds to assignment (see 7.5)
  - `hoax` → `reports.status` becomes `ditolak`; a `report_status_histories` row is written (`old_status = menunggu`, `new_status = ditolak`); citizen is deducted points (see Section 9) and notified
- Every verification decision **must** write a `report_status_histories` entry (immutable audit trail)

---

### 7.5 Report Assignment (Admin)

- Admin selects one active `petugas` and creates a `report_assignments` row:
  - `report_id`, `officer_id`, `assigned_by` (admin's user id), `assigned_at`, `status = ditugaskan`
- On assignment, `reports.status` transitions `menunggu → diproses`
- A `report_status_histories` entry is recorded for this transition
- A `notifications` row is created for the assigned officer

---

### 7.6 Field Handling (Petugas)

- Officer views assigned reports (`report_assignments` where `officer_id = auth user`)
- Officer accepts the task → `report_assignments.accepted_at` set, `status = diproses`
- After resolving in the field, officer:
  - uploads `proof_photo`
  - adds `note`
  - sets `report_assignments.status = selesai`, `finished_at = now()`
- On completion, `reports.status` transitions `diproses → selesai`
- A `report_status_histories` entry is written (`new_status = selesai`, `proof_photo`, `note` carried over)
- `notifications` rows are created for both the citizen and the admin

---

### 7.7 Points & Gamification

- Every report reaching `valid` verification generates one `user_points` row: `type = tambah`, `points = +10`, linked to `report_id`
- Every report marked `hoax` generates one `user_points` row: `type = kurang`, `points = -10` (or higher penalty, see Section 9), linked to `report_id`
- `users.total_points` is recalculated as `SUM(user_points.points where type=tambah) - SUM(user_points.points where type=kurang)` — recommendation: update it transactionally whenever a `user_points` row is inserted, rather than computing on every read
- Leaderboard = `users` ordered by `total_points DESC`, top 10 displayed
- **Badge auto-award logic**: after every `user_points` insert, check all `badges` where `required_points <= users.total_points` AND `required_reports <= (count of user's valid reports)`; for each newly-qualifying badge not already in `user_badges`, insert a row with `earned_at = now()` and trigger a notification

---

### 7.8 Rewards & Redemption

- Citizen browses active (`status = true`) rewards where `stock > 0`
- Citizen redeems a reward if `users.total_points >= rewards.point_cost`:
  - Insert `reward_redemptions` row: `status = pending`, `points_used = rewards.point_cost`, `redeemed_at = now()`
  - Insert a corresponding `user_points` row (`type = kurang`, `points = rewards.point_cost`) to deduct the cost
  - Decrement `rewards.stock` by 1
- Admin updates `reward_redemptions.status`: `pending → approved → taken`, or `pending → rejected` (if rejected, points **must** be refunded via a new `user_points` `tambah` entry and `stock` restored)

---

### 7.9 Notifications

- `notifications` are created (not sent externally) on:
  - Report submitted (confirmation to citizen)
  - Report verified as hoax (to citizen)
  - Report assigned (to officer)
  - Report resolved (to citizen & admin)
  - Badge earned (to citizen)
  - Reward redemption status change (to citizen)
- `is_read` toggled by the citizen when viewed in the notification bell/list

---

**### Relationship Between Report Status and Assignment Status

reports.status and report_assignments.status are separate state machines:

New report: reports.status = menunggu, verification_status = pending.

Admin verifies valid: report remains menunggu until an assignment is created.

Admin creates assignment: reports.status becomes diproses; assignment starts as ditugaskan.

Officer accepts assignment: assignment becomes diproses; report remains diproses.

Officer completes assignment: assignment becomes selesai; report becomes selesai.

Hoax verification: report becomes ditolak; no active assignment may remain.

This distinction must be preserved in backend services and frontend status displays.

7.10 Business Rules**

- A report's `status` may only follow this path: `menunggu → diproses → selesai`, or `menunggu → ditolak`. No other transitions are valid.
- A report cannot be assigned (`report_assignments` created) unless `verification_status = valid`.
- A report cannot move to `selesai` unless it has an active `report_assignments` row with `status = diproses`.
- Every `reports.status` change **must** produce exactly one `report_status_histories` row (no silent status updates).
- `users.total_points` must never be edited outside of a `user_points` insert (no direct column update from application code).
- Reward redemption is blocked if `users.total_points < rewards.point_cost` or `rewards.stock <= 0` or `rewards.status = false`.
- Only one **active** assignment (`status != selesai`) may exist per report at a time.

---

### 7.11 Edge Cases

- Citizen uploads a photo the AI API fails to classify (timeout/error) → `ai_category`/`ai_confidence` remain null, citizen must pick category manually; report submission must not be blocked by AI failure
- Admin verifies a report as `hoax` after it was already assigned to an officer → assignment must be cancelled/invalidated and the officer notified
- Reward redemption approved but stock ran out between browse and redeem (race condition) → must be handled with a DB-level check/lock on `rewards.stock` before decrementing
- A rejected `reward_redemptions` request must fully reverse its points deduction and stock decrement
- Duplicate reports for the same real-world issue (multiple citizens reporting the same pothole) — `comments` feature allows cross-referencing, but deduplication logic is **out of scope** for this phase (manual admin judgment only)

---

## 8. Report Processing Logic

### System Flow

```
Citizen submits Report
        ↓
 status = menunggu, verification_status = pending
 (AI classification attempted, non-blocking)
        ↓
 Admin verifies
   ┌────┴─────┐
   ▼          ▼
 valid       hoax
   │          │
   ▼          ▼
Admin assigns   status = ditolak
Petugas         (points -10, notify citizen)
   │
   ▼
status = diproses
report_assignments.status = ditugaskan → diproses
   │
   ▼ (Officer resolves in the field)
report_assignments.status = selesai
status = selesai
(points +10 already granted at "valid" step — see Section 9)
notify citizen & admin
```

### Report Eligibility for Assignment

A report can be assigned to an officer **only if all conditions are met**:
1. `reports.verification_status = 'valid'`
2. `reports.status = 'menunggu'`
3. No existing `report_assignments` row with `status != 'selesai'` for this report

### Report Exclusion Conditions

A report **cannot** be assigned/progressed if any of the following apply:
- `verification_status` is still `pending` or is `hoax`
- `status` is already `diproses`, `selesai`, or `ditolak`
- An active (non-`selesai`) assignment already exists

---

## 9. Points Calculation Logic

### Points per Valid Report
```
points_awarded = +10   (user_points.type = 'tambah', linked to report_id)
```

### Points per Hoax Report
```
points_deducted = -10  (user_points.type = 'kurang', linked to report_id)
```

### Running Total (denormalized on `users.total_points`)
```
total_points = SUM(points where type = 'tambah')
             - SUM(points where type = 'kurang')
```
`total_points` must never go negative in the UI — clamp display to 0 if the running sum dips below 0, but store the raw ledger as-is in `user_points` for auditability.

### Badge Qualification Check (run after every points change)
```
FOR EACH badge WHERE
    badge.required_points  <= user.total_points
    AND badge.required_reports <= user.valid_report_count
  IF badge NOT IN user_badges (for this user)
    INSERT user_badges (user_id, badge_id, earned_at = now())
    CREATE notification "Badge earned: {badge.name}"
```

---

## 10. Example Scenarios

### Scenario A — Valid Report, Successfully Resolved
1. Citizen submits report → `status=menunggu`, `verification_status=pending`
2. Admin marks `valid` → `user_points` +10 inserted → `total_points` updated → badge check runs
3. Admin assigns officer → `status=diproses`, `report_assignments.status=ditugaskan`
4. Officer accepts → `report_assignments.status=diproses`
5. Officer resolves, uploads proof → `report_assignments.status=selesai`, `finished_at` set
6. `reports.status=selesai`, `report_status_histories` entry written, notifications sent to citizen & admin

### Scenario B — Hoax Report
1. Citizen submits report → `status=menunggu`, `verification_status=pending`
2. Admin marks `hoax` → `reports.status=ditolak`
3. `user_points` −10 inserted → `total_points` updated (never displayed below 0)
4. `report_status_histories` entry written (`old_status=menunggu`, `new_status=ditolak`)
5. Notification sent to citizen explaining rejection

### Scenario C — Reward Redemption Rejected
1. Citizen has `total_points = 150`, redeems a reward costing 100 points
2. `reward_redemptions` row created (`status=pending`), `user_points` −100 inserted, `rewards.stock` −1
3. Admin rejects the redemption (e.g. out of stock in reality)
4. System reverses: `user_points` +100 inserted (refund), `rewards.stock` +1, `reward_redemptions.status=rejected`

---

## 11. UI Modules

### Core Modules
- Landing Page (public stats, latest reports, how-it-works)
- Report Creation & History (Citizen)
- Report Verification & Assignment (Admin)
- Officer Task Board (Petugas)
- Interactive Map (Leaflet.js — all reports, filterable by category/district/status)
- Leaderboard & Badges (Citizen)
- Rewards Catalog & Redemption (Citizen) / Redemption Management (Admin)
- Master Data Management: Categories, Districts, Villages, Badges, Rewards (Admin)
- User & Role Management (Admin)

### UI Rules
- Each module must be implemented as reusable Vue components
- Backend and frontend validation are mandatory
- Consistent layout using Tailwind CSS + DaisyUI
- Mobile-first responsive design
- Default language: **Bahasa Indonesia** (matches enum values already used in the schema, e.g. `menunggu`, `diproses`, `selesai`, `ditolak`)

---

## 12. Security & Validation

- Role and permission management via Spatie (`admin`, `petugas`, `masyarakat`)
- Backend validation mandatory on every write endpoint (report creation, verification, assignment, redemption)
- File upload validation: image type + max size (2MB) for `reports.photo`, `report_images.image`, `report_assignments.proof_photo`
- Geolocation values (`latitude`, `longitude`) validated as within Depok's bounding box (soft warning, not a hard block)
- Rate-limit report submission per citizen to discourage spam/duplicate-hoax abuse

---

## 13. Acceptance Criteria

- A report can never skip a status (e.g. `menunggu` directly to `selesai`)
- Every status change produces exactly one `report_status_histories` row
- A report can only be assigned once it is `verification_status = valid`
- Points are only ever modified through `user_points` inserts, never a direct `users.total_points` update
- Badge awarding is automatic and idempotent (no duplicate `user_badges` rows for the same user+badge)
- Reward redemption respects `stock` and `point_cost`, and rejection always reverses points/stock
- Leaderboard always reflects the current `users.total_points` ranking
- Role-based access control correctly restricts each module to its intended role(s)

---

## 14. Future Enhancements (Out of Scope)

- Real government system integration (SIPD)
- Real reward fulfillment via payment gateway / e-voucher partner API
- Automated duplicate-report detection/merging
- Push/email/SMS notifications
- Multi-city expansion beyond Depok
- WebSocket-based real-time dashboard (out of MVP; current MVP uses normal requests/polling where needed)

---

**## 15. Migration Table (as implemented)

Important: The SQL-like schema below is documentation of the migrations, not a replacement for the actual Laravel migration files. If any detail differs from the real migration files in /database/migrations, the real migration files take precedence. Do not modify migrations automatically.**

```sql
Table users {
  id integer [primary key]
  name varchar [not null]
  email varchar [not null, unique]
  email_verified_at timestamp
  password varchar [not null]
  phone varchar(20)
  profile_photo varchar
  total_points integer [not null, default: 0]
  remember_token varchar
  created_at timestamp
  updated_at timestamp
}

Table categories {
  id integer [primary key]
  name varchar [not null]
  description text
  icon varchar
  created_at timestamp
  updated_at timestamp
}

Table districts {
  id integer [primary key]
  name varchar [not null]
  created_at timestamp
  updated_at timestamp
}

Table villages {
  id integer [primary key]
  district_id integer [not null, ref: > districts.id]
  name varchar [not null]
  created_at timestamp
  updated_at timestamp
}

Table reports {
  id integer [primary key]
  user_id integer [not null, ref: > users.id]
  category_id integer [ref: > categories.id]
  district_id integer [ref: > districts.id]
  village_id integer [ref: > villages.id]
  title varchar [not null]
  description text [not null]
  photo varchar [not null]
  latitude decimal(10,8) [not null]
  longitude decimal(11,8) [not null]
  address text [not null]
  ai_category varchar
  ai_confidence decimal(5,2)
  ai_response text
  classified_at timestamp
  verification_status enum('pending','valid','hoax') [not null, default: 'pending']
  status enum('menunggu','diproses','selesai','ditolak') [not null, default: 'menunggu']
  created_at timestamp
  updated_at timestamp
}

Table report_images {
  id integer [primary key]
  report_id integer [not null, ref: > reports.id]
  image varchar [not null]
  uploaded_at timestamp
  created_at timestamp
  updated_at timestamp
}

Table report_assignments {
  id integer [primary key]
  report_id integer [not null, ref: > reports.id]
  officer_id integer [not null, ref: > users.id]
  assigned_by integer [not null, ref: > users.id]
  assigned_at timestamp
  accepted_at timestamp
  finished_at timestamp
  status enum('ditugaskan','diproses','selesai') [not null, default: 'ditugaskan']
  proof_photo varchar
  note text
  created_at timestamp
  updated_at timestamp
}

Table report_status_histories {
  id integer [primary key]
  report_id integer [not null, ref: > reports.id]
  changed_by integer [not null, ref: > users.id]
  old_status enum('menunggu','diproses','selesai','ditolak') [not null]
  new_status enum('menunggu','diproses','selesai','ditolak') [not null]
  note text
  proof_photo varchar
  changed_at timestamp [not null, default: `now()`]
  created_at timestamp
  updated_at timestamp
}

Table comments {
  id integer [primary key]
  report_id integer [not null, ref: > reports.id]
  user_id integer [not null, ref: > users.id]
  comment text [not null]
  created_at timestamp
  updated_at timestamp
}

Table notifications {
  id integer [primary key]
  user_id integer [not null, ref: > users.id]
  title varchar [not null]
  message text [not null]
  is_read boolean [not null, default: false]
  created_at timestamp
  updated_at timestamp
}

Table user_points {
  id integer [primary key]
  user_id integer [not null, ref: > users.id]
  report_id integer [ref: > reports.id]
  points integer [not null]
  type enum('tambah','kurang') [not null]
  description text
  created_at timestamp
  updated_at timestamp
}

Table badges {
  id integer [primary key]
  name varchar [not null]
  description text
  required_points integer [not null, default: 0]
  required_reports integer [not null, default: 0]
  icon varchar
  created_at timestamp
  updated_at timestamp
}

Table user_badges {
  id integer [primary key]
  user_id integer [not null, ref: > users.id]
  badge_id integer [not null, ref: > badges.id]
  earned_at timestamp
  created_at timestamp
  updated_at timestamp
}

Table rewards {
  id integer [primary key]
  name varchar [not null]
  reward_type enum('voucher','pulsa','bibit','lainnya') [not null]
  description text
  point_cost integer [not null]
  stock integer [not null, default: 0]
  image varchar
  status boolean [not null, default: true]
  created_at timestamp
  updated_at timestamp
}

Table reward_redemptions {
  id integer [primary key]
  reward_id integer [not null, ref: > rewards.id]
  user_id integer [not null, ref: > users.id]
  points_used integer [not null]
  status enum('pending','approved','rejected','taken') [not null, default: 'pending']
  redeemed_at timestamp
  created_at timestamp
  updated_at timestamp
}

// Spatie Permission tables (roles, permissions, model_has_roles,
// model_has_permissions, role_has_permissions) follow the standard
// spatie/laravel-permission package schema and are not customized.
```

---

## 16. Frontend Page Specifications (for AI/Developer UI Generation)

This section exists so an AI code generator (or a frontend developer) can build actual screens — not just infer them from the backend schema. Every field below is mapped to a real column from Section 15, and every page states its route, access role, layout, states, and interactions. Follow the design tokens in Section 10 (color palette, typography, components) for all pages.

**Global conventions for every authenticated page:**
- Left sidebar navigation (collapsible on mobile → becomes bottom nav or hamburger drawer), items vary per role (see each dashboard below)
- Top bar: page title/greeting, notification bell (unread count from `notifications.is_read = false`), user avatar/name dropdown (`profile_photo`, `name`) with logout
- Every list/table view needs three explicit states: **loading** (skeleton), **empty** (illustration + short message + primary CTA), **error** (retry button)
- Every form needs: inline field-level validation errors, a disabled+spinner submit button while pending, and a success toast on completion
- All timestamps displayed as relative time ("2 jam lalu") with full datetime on hover

---

### 16.1 Landing Page
**Route:** `/` · **Access:** Public

| Section | Components | Data Source |
|---|---|---|
| Navbar | Logo "UrbanEye", nav links (Beranda, Fitur, Cara Kerja, Tentang, Kontak), Login button (outline), Daftar button (filled) | Static |
| Hero | Headline "Bangun Kota Lebih Bersih Bersama UrbanEye", subheadline, 2 CTA buttons ("Laporkan Sekarang" → `/register` or `/reports/create` if logged in, "Pelajari Lebih Lanjut" → scroll to Cara Kerja) | Static |
| Hero — mini dashboard preview | Mock browser window containing: live map (Leaflet, small), 3 stat pills (Total Laporan, Diproses, Selesai), "Laporan Terbaru" mini-list (3 items) | `COUNT(reports)`, `COUNT(reports WHERE status=diproses)`, `COUNT(reports WHERE status=selesai)`, latest 3 `reports` ordered by `created_at DESC` |
| "Mengapa Memilih UrbanEye?" | 4 feature cards (icon + title + description): Berbasis Lokasi, Upload Foto Bukti, Realtime Tracking, Notifikasi Otomatis | Static |
| "Cara Kerja UrbanEye" | 4 numbered steps with connecting line: Laporkan Masalah → Tentukan Lokasi → Lampirkan Bukti → Kirim Laporan | Static |
| Footer | CTA banner + 3 columns (Navigasi, Informasi, Ikuti Kami) + social icons + copyright | Static |

**States:** map/stat section shows skeleton while stats load; falls back to "0" gracefully if empty (new deployment).

---

### 16.2 Register Page
**Route:** `/register` · **Access:** Public (guest only — redirect logged-in users to their dashboard)

**Layout:** Split screen — left: brand panel (headline "Bersama Wujudkan Kota Lebih Bersih Dan Hijau", 3 benefit bullets with icons, illustration); right: form card.

| Field | Input Type | Maps to Column | Validation |
|---|---|---|---|
| Nama Lengkap | text | `users.name` | required |
| Email | email | `users.email` | required, valid email, unique |
| No. Telepon | tel | `users.phone` | optional, numeric |
| Password | password (show/hide toggle) | `users.password` | required, min 8 chars |
| Konfirmasi Password | password (show/hide toggle) | — (client-side only) | must match Password |
| Syarat & Ketentuan | checkbox | — | required to be checked |

**Actions:** "Daftar" button (primary, full width) → creates user with default role `masyarakat`, redirects to `/login` with success toast. "Sudah punya akun? Login di sini" link → `/login`.

---

### 16.3 Login Page
**Route:** `/login` · **Access:** Public (guest only)

**Layout:** Same split screen as Register, right panel form.

| Field | Input Type | Maps to Column |
|---|---|---|
| Email | email | `users.email` |
| Password | password (show/hide toggle) | `users.password` |
| Ingat saya | checkbox | Sanctum "remember" cookie |

**Actions:** "Lupa password?" link (forgot-password flow, out of MVP scope — can stub), "Login" primary button, "Masuk dengan Google" secondary button (Socialite, optional/stretch), "Belum punya akun? Daftar sekarang" link.

**Post-login redirect logic:** route by role — `admin` → `/admin/dashboard`, `petugas` → `/petugas/dashboard`, `masyarakat` → `/dashboard`.

---

**### MVP exclusions for authentication

Forgot-password backend flow is out of MVP scope.

Google OAuth / Socialite is out of MVP scope.

Do not install or configure additional OAuth/email infrastructure unless explicitly requested.

16.4 Citizen Dashboard Shell**

**Sidebar items (role = masyarakat):** Dashboard, Buat Laporan, Riwayat Laporan, Leaderboard, Reward Saya, Pengaturan, Bantuan
**Top bar greeting:** "Hallo, {users.name}!"

---

### 16.5 Create Report Form
**Route:** `/reports/create` · **Access:** masyarakat

| Field | Input Type | Maps to Column | Notes |
|---|---|---|---|
| Judul Laporan | text | `reports.title` | required |
| Kategori Laporan | select dropdown | `reports.category_id` | options from `categories` table; pre-filled with `ai_category` suggestion once photo is analyzed, user can override |
| Deskripsi Laporan | textarea w/ char counter | `reports.description` | required, e.g. max 500 chars shown as "120/500" |
| Upload Foto | drag & drop zone, thumbnail grid, "+" to add more | primary photo → `reports.photo`; extra photos → `report_images` rows | JPG/PNG, max 5MB each per proposal mock-up (schema allows any size — enforce in frontend + backend) |
| Lokasi Laporan | embedded Leaflet map, draggable pin, "use current location" GPS button | `reports.latitude`, `reports.longitude` | reverse-geocode to prefill `reports.address` (editable text) and try to auto-match `reports.district_id`/`village_id` |

**Flow after photo upload:** show a small "Menganalisa foto..." inline loader → call AI classification endpoint → populate `ai_category`/`ai_confidence` badge next to the Kategori dropdown (e.g. "AI menyarankan: Sampah (87%)") without forcing the selection.

**Actions:** "Batal" (secondary, returns to dashboard, confirm if form is dirty), "Kirim Laporan" (primary, disabled until required fields valid) → on success: toast "Laporan berhasil dikirim! +10 poin menunggu verifikasi", redirect to `/reports/history`.

---

### 16.6 Report History
**Route:** `/reports/history` · **Access:** masyarakat

- List/card view of the citizen's own reports, each card shows: thumbnail (`reports.photo`), `title`, category badge, status badge (color per `reports.status`: menunggu=gray, diproses=blue, selesai=green, ditolak=red), relative `created_at`
- Filter/tabs by `status`; search by `title`
- Click a card → **Report Detail modal/page**:
  - Full description, all `report_images`, map pin of the location
  - **Status timeline** (vertical stepper) built from `report_status_histories` ordered by `changed_at` — each step shows `new_status`, `note`, `proof_photo` (if any), `changed_at`, and who changed it (`changed_by` → `users.name`)
  - `comments` thread below (list + input box to add a new comment)
- Empty state: "Belum ada laporan. Yuk buat laporan pertamamu!" + CTA button to `/reports/create`

---

### 16.7 Leaderboard
**Route:** `/leaderboard` · **Access:** masyarakat (view own rank), visible read-only to all logged-in roles

- Table/list of top 10 users ordered by `users.total_points DESC`: rank number/medal icon for top 3, `profile_photo`, `name`, `total_points`
- Highlight current user's row even if outside top 10, with their exact rank shown separately below the table

---

### 16.8 Reward & Points Page
**Route:** `/rewards` · **Access:** masyarakat

| Section | Components | Data Source |
|---|---|---|
| Stat cards | "Poin Saya" (`users.total_points`), "Level Saya" (derived label from a points-range mapping, e.g. Eco Hero), "Peringkat Saya" (rank from leaderboard query) | `users`, computed |
| Progress bar | "X / Y poin menuju reward berikutnya" | `users.total_points` vs. cheapest un-owned `rewards.point_cost` above current total |
| Cara Mendapatkan Poin | Static checklist (laporan terverifikasi +10, laporan hoax -10, dst.) | Static |
| Reward yang Bisa Ditukar | Horizontal carousel of reward cards: image, name, `point_cost`, "Tukar" button (disabled if `total_points < point_cost` or `stock <= 0`) | `rewards WHERE status=true AND stock>0` |
| Riwayat Poin | List with icon per entry (↑ green for `tambah`, ↓ red for `kurang`), `description`, amount, relative date | `user_points` ordered by `created_at DESC` |

**Redeem flow:** clicking "Tukar" opens a confirmation modal ("Tukar 100 poin untuk Voucher Pulsa Rp25.000?") → on confirm, creates `reward_redemptions` (status `pending`) → toast "Permintaan penukaran terkirim, menunggu persetujuan admin" → item moves into Riwayat Poin as a pending deduction.

---

### 16.9 Admin Dashboard Shell
**Sidebar items (role = admin):** Dashboard, Kelola Laporan, Kelola Masyarakat, Statistik & Grafik, Peta Monitoring, Reward & Leaderboard, Pengaturan Sistem, Laporan & Export

---

### 16.10 Admin — Dashboard (Overview)
**Route:** `/admin/dashboard` · **Access:** admin

- 4 stat cards: Total Laporan (`COUNT(reports)`), Laporan Mendesak (e.g. `verification_status=pending` older than X hours, or a manually-flagged urgent set), Dalam Proses (`status=diproses`), Selesai (`status=selesai`)
- "Laporan Terbaru" list (latest 5 `reports`, with status badge + "Lihat Semua" → Kelola Laporan)
- "Laporan per Kategori" donut chart — `COUNT(reports) GROUP BY category_id`
- "Aktivitas Petugas" feed — recent `report_status_histories`/`report_assignments` changes made by `petugas` users, with relative time

---

### 16.11 Admin — Kelola Laporan (Manage Reports)
**Route:** `/admin/reports` · **Access:** admin

- Table with filters: `status`, `verification_status`, `category_id`, `district_id`, search by `title`
- Columns: thumbnail, title, category, citizen name, district/village, status badge, submitted date, action button "Detail"
- **Report Detail drawer/page:**
  - Full report info, photo gallery, map pin
  - If `verification_status = pending`: two buttons — **"Verifikasi Valid"** and **"Tolak (Hoax)"** (Tolak requires a reason text field → stored as `report_status_histories.note`)
  - If `verification_status = valid` and no active assignment: **"Tugaskan Petugas"** → dropdown of active `petugas` users → confirm → creates `report_assignments`
  - If assigned: show current assignment status (`ditugaskan`/`diproses`/`selesai`), assigned officer name, `proof_photo`, `note` once resolved
  - Full `report_status_histories` timeline (same component as 16.6)

---

### 16.12 Admin — Kelola Masyarakat (Manage Users)
**Route:** `/admin/users` · **Access:** admin

- Table of `users` with role filter (Spatie), search by name/email
- Columns: avatar, name, email, role badge, `total_points`, joined date, action (edit role/permissions, deactivate)
- "Tambah User" button → modal form to create a user directly with a chosen role (used for creating `admin`/`petugas` accounts)

---

### 16.13 Admin — Statistik & Grafik
**Route:** `/admin/statistics` · **Access:** admin

- Bar chart: reports per category (`categories.name` x count)
- Line chart: weekly report trend (`reports.created_at` grouped by week)
- Table/map breakdown: reports per `district`/`village`
- Export button (CSV/PDF) — ties to "Laporan & Export" module

---

### 16.14 Admin — Peta Monitoring
**Route:** `/admin/map` · **Access:** admin

- Full-screen Leaflet map, all `reports` plotted by `latitude`/`longitude`, marker color by `status`
- Sidebar/legend with counts per status, click marker → mini popup (title, category, status, "Lihat Detail" → 16.11 detail view)

---

### 16.15 Admin — Reward & Leaderboard Management
**Route:** `/admin/rewards` · **Access:** admin

- CRUD table for `rewards` (name, type, point_cost, stock, status toggle, image)
- Separate tab/table: `reward_redemptions` queue — filter by `status`; actions "Approve", "Reject" (reject triggers the points/stock reversal described in Section 7.8), "Mark as Taken"
- Read-only leaderboard view (same component as 16.7)
- CRUD table for `badges` (name, description, required_points, required_reports, icon)

---

### 16.16 Petugas Dashboard Shell
**Sidebar items (role = petugas):** Dashboard, Tugas Saya, Peta Laporan, Riwayat Penanganan, Statistik, Pengaturan, Bantuan
**Top bar greeting:** "Hallo, Petugas! 👋"

---

### 16.17 Petugas — Dashboard (Overview)
**Route:** `/petugas/dashboard` · **Access:** petugas

- 4 stat cards: Tugas Hari Ini (`report_assignments WHERE officer_id=auth AND status!=selesai`), Sedang Dikerjakan (`status=diproses`), Sudah Selesai (`status=selesai`, count), Menunggu Verifikasi (assignments just created, `accepted_at IS NULL`)
- "Tugas Prioritas" list: report title, address, `assigned_at` date, urgency badge, "Lihat Detail" button
- "Aktifitas Hari Ini" feed: today's own status changes from `report_status_histories`/`report_assignments`
- "Peta Laporan" — small map filtered to this officer's assigned reports, legend by urgency/status, "Lihat Peta Lengkap" → 16.19

---

### 16.18 Petugas — Tugas Saya (My Tasks) & Task Detail
**Route:** `/petugas/tasks` · **Access:** petugas

- List of `report_assignments` for the logged-in officer, grouped/tabbed by `status` (ditugaskan / diproses / selesai)
- **Task Detail view:**
  - Report info (photo, description, location map, citizen name)
  - If `status = ditugaskan`: "Terima Tugas" button → sets `accepted_at`, `status = diproses`
  - If `status = diproses`: form to complete — Catatan Penanganan (textarea → `report_assignments.note`), Upload Foto Bukti (→ `report_assignments.proof_photo`), "Tandai Selesai" button → sets `finished_at`, `status = selesai`, cascades `reports.status = selesai` and writes a `report_status_histories` row

---

### 16.19 Petugas — Peta Laporan
**Route:** `/petugas/map` · **Access:** petugas

- Same map component as 16.14, scoped to this officer's assignments only

---

### 16.20 Petugas — Riwayat Penanganan
**Route:** `/petugas/history` · **Access:** petugas

- List of this officer's completed (`status=selesai`) assignments: report title, `finished_at`, `proof_photo` thumbnail, note preview

---

### 16.21 Component Reuse Map (build once, use across roles)

| Shared Component | Used In |
|---|---|
| Status Badge (color-coded) | 16.6, 16.10, 16.11, 16.17, 16.18 |
| Report Card | 16.6, 16.10 (as "Laporan Terbaru"), 16.11 |
| Status Timeline / Stepper | 16.6, 16.11 |
| Leaflet Map + Marker Legend | 16.1, 16.5, 16.14, 16.17, 16.19 |
| Stat Card | 16.8, 16.10, 16.17 |
| Photo Upload / Gallery | 16.5, 16.18 |
| Leaderboard Table | 16.7, 16.15 |
| Notification Bell + Dropdown | All authenticated shells |
| Empty/Loading/Error State wrapper | Every list view |

---

### 16.22 Suggested AI-Generation Order (frontend)

1. Design tokens setup (Tailwind config with the color palette + Poppins font from Section 10)
2. Shared/reusable components (16.21) first — everything else depends on them
3. Public pages: Landing (16.1), Register (16.2), Login (16.3)
4. Citizen flow: Dashboard shell (16.4) → Create Report (16.5) → History (16.6) → Leaderboard (16.7) → Rewards (16.8)
5. Admin flow: Dashboard shell (16.9) → Overview (16.10) → Kelola Laporan (16.11) → Kelola Masyarakat (16.12) → Statistik (16.13) → Peta (16.14) → Reward Management (16.15)
6. Petugas flow: Dashboard shell (16.16) → Overview (16.17) → Tugas Saya (16.18) → Peta (16.19) → Riwayat (16.20)

This order lets an AI generator (or a developer) ship a working, demoable citizen flow first, then layer in the admin and officer sides — matching how the MVP would actually be judged/demoed.