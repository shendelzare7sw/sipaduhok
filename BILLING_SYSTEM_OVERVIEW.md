# Sipaduhok Billing System (Tagihan) - Comprehensive Overview

## Executive Summary
The billing system manages student fees (tagihan) across three user roles:
- **Admin/Bendahara**: Create, manage, and validate bills
- **Students/Parents**: View bills and make payments
- **System**: Automatically track payment status and control access

---

## 1. Database Models & Relationships

### Primary Model: **Tagihan** (Bill)
**Location**: `app/Models/Tagihan.php`
**Database Table**: `tagihan`

**Key Fields**:
| Field | Type | Purpose |
|-------|------|---------|
| `id` | int | Primary key |
| `siswa_id` | FK | Links to Siswa (student) |
| `tahun_ajaran_id` | FK | Links to TahunAjaran (academic year) |
| `jenis_tagihan` | string | Billing type (see table below) |
| `keterangan` | string | Description/notes |
| `jumlah` | decimal | Amount owed |
| `tanggal_jatuh_tempo` | date | Due date |
| `status` | enum | Payment status (belum_bayar, cicilan, sudah_bayar, terlambat) |

**Supported Billing Types (jenis_tagihan)**:
| Code | Label | Notes |
|------|-------|-------|
| `uang_pendaftaran` | Formulir Pendaftaran/Daftar Ulang | Registration |
| `uang_pangkal` | Uang Pangkal | Capital/Initial fee |
| `kegiatan` | Uang Kegiatan | Activity fee |
| `buku` | Buku Paket | Book package |
| `seragam` | Seragam | Uniform |
| `rapor_foto` | Rapor Foto | Report card photo |
| `ujian` | Ujian & Wisuda | Exam & graduation |
| `akm` | AKM | AKM assessment |
| `spp_*` | SPP | Monthly SPP (e.g., spp_januari, spp_februari) |
| `custom` | Any custom name | User-defined types |

**Relationships**:
```php
- belongsTo(Siswa)           // Student who owes the bill
- belongsTo(TahunAjaran)     // Academic year context
- hasMany(Pembayaran)        // Related payments
```

**Key Methods**:
- `updateStatusBayar()` - Auto-update status based on payment validation
- `getLabelJenis($jenis)` - Convert jenis_tagihan code to human-readable text

---

### Related Model: **Pembayaran** (Payment)
**Location**: `app/Models/Pembayaran.php`

**Key Fields**:
- `tagihan_id` - References Tagihan
- `siswa_id` - References Siswa
- `jumlah_bayar` - Amount paid
- `status_validasi` - (disetujui, ditolak, pending)
- Related to payment gateway (Midtrans)

**Note**: Only payments with `status_validasi = 'disetujui'` count toward bill payment.

---

### Related Models:
1. **Siswa** - `hasMany(Tagihan)` - Student has many bills
2. **TahunAjaran** - `hasMany(Tagihan)` - Academic year groups bills
3. **PengaturanBatasPembayaran** - Payment requirements
   - Uses `jenis_tagihan_required` array to specify which bills must be paid
   - Used for access control (exam/rapor)

---

## 2. Controllers Structure

### Controller Hierarchy
```
BendaharaTagihanController (Base Implementation)
    ├── Admin\Keuangan\TagihanController (extends, overrides views)
    └── Bendahara\TagihanController (inherits)
```

### Admin Billing Controllers
**Path**: `app/Http/Controllers/Admin/Keuangan/`

#### 1. **TagihanController.php** ⭐ Main Controller
**Route Prefix**: `admin.keuangan.tagihan`

**Methods & Routes**:

| Method | Route | HTTP | Purpose |
|--------|-------|------|---------|
| `index()` | `/` | GET | List students with bill summary |
| `show()` | `/{siswa}` | GET | View student's bill details |
| `edit()` | `/{siswa}/edit` | GET | Bill editing form |
| `update()` | `/{siswa}` | PUT | Save bill changes |
| `bulkCreate()` | `/bulk-create` | GET/POST | Mass create bills |
| `createCustom()` | `/create-custom` | GET | Custom bill form |
| `storeCustom()` | `/store-custom` | POST | Save custom bill |
| `generateSppForm()` | `/generate-spp` | GET | SPP generation form |
| `generateSpp()` | `/generate-spp` | POST | Create 12 monthly SPP bills |
| `duplicateForm()` | `/duplicate` | GET | Duplicate form |
| `duplicate()` | `/duplicate` | POST | Duplicate bills |
| `importForm()` | `/import` | GET | Excel import form |
| `import()` | `/import` | POST | Process Excel import |
| `downloadTemplate()` | `/template` | GET | Download Excel template |
| `cetak()` | `/{siswa}/cetak` | GET | Print invoice |
| `cetakLaporan()` | `/cetak-laporan` | GET | Print bill report |
| `destroyItem()` | `/{tagihan}/destroy-item` | DELETE | Delete single bill |
| `getSiswaByKelas()` | `/api/siswa-by-kelas/{kelas}` | GET | API - Get students by class |
| `getTagihanPreview()` | `/api/tagihan-preview/{siswa}` | GET | API - Get bill preview |

#### 2. **PembayaranController.php**
- Manages payment validation
- Methods: `validasi()`, `validasiLangsung()`, `riwayatSiswa()`

#### 3. **InfoPembayaranController.php**
- Payment information management

#### 4. **LaporanPembayaranController.php**
- Payment report generation

#### 5. **ValidasiAksesController.php**
- Validate student access based on payment status
- Used for exam/rapor access control

---

### Bendahara Billing Controllers
**Path**: `app/Http/Controllers/Bendahara/`

**Same as Admin** - Controllers in this folder provide the base implementation that Admin controllers inherit from.

- **TagihanController.php** - Base bill management
- **PembayaranController.php** - Payment processing
- **LaporanPembayaranController.php** - Reports
- **ValidasiAksesController.php** - Access validation
- **BendaharaController.php** - Dashboard
- **PromotionValidationController.php** - Promotion validation based on payments

---

### Parent Controller
**Path**: `app/Http/Controllers/OrangTua/`

**OrangTuaController.php**
- Methods:
  - `dashboard()` - Parent dashboard
  - `tagihanAnak()` - View child's bills
  - `prosesBayar()` - Process single payment
  - `processBulkPay()` - Process multiple payments at once
  - `snapPayment()` - Midtrans payment gateway integration
  - `snapFinish()` - Payment completion callback
  - `continuePayment()` - Resume payment
  - `cetakInvoice()` - Print payment invoice

---

## 3. Routes Configuration

### Admin Billing Routes
**File**: `routes/web.php` (lines 435-480)
**Prefix**: `/admin/keuangan/tagihan`
**Middleware**: `role:admin`

```
GET     /                           → index (List all bills)
GET     /import                     → importForm
POST    /import                     → import
GET     /template                   → downloadTemplate
GET     /bulk-create                → bulkCreate
POST    /bulk-create                → bulkCreate (store)
GET     /create-custom              → createCustom
POST    /store-custom               → storeCustom
GET     /generate-spp               → generateSppForm
POST    /generate-spp               → generateSpp
GET     /duplicate                  → duplicateForm
POST    /duplicate                  → duplicate
GET     /api/siswa-by-kelas/{kelas} → getSiswaByKelas (API)
GET     /api/tagihan-preview/{siswa} → getTagihanPreview (API)
GET     /cetak-laporan              → cetakLaporan
GET     /{siswa}                    → show (Detail)
GET     /{siswa}/edit               → edit (Edit form)
PUT     /{siswa}                    → update (Save)
DELETE  /{tagihan}/destroy-item     → destroyItem
GET     /{siswa}/cetak              → cetak (Print)
```

### Bendahara Billing Routes
**File**: `routes/web.php` (lines 938-1025)
**Prefix**: `/bendahara/tagihan`
**Middleware**: `role:bendahara`

**Similar to Admin routes**

### Parent Billing Routes
**File**: `routes/web.php` (lines 1450-1472)
**Prefix**: `/orang-tua/tagihan`
**Middleware**: `role:orang_tua`

```
GET     /anak/{siswa}                    → tagihanAnak (View child's bills)
POST    /anak/{siswa}/bayar              → prosesBayar (Pay single)
POST    /anak/{siswa}/bulk-pay           → processBulkPay (Pay multiple)
GET     /snap-finish                     → snapFinish (Payment complete)
GET     /snap/{pembayaran}               → snapPayment (Midtrans gateway)
POST    /continue/{pembayaran}           → continuePayment
GET     /{pembayaran}/invoice            → cetakInvoice (Print invoice)
```

---

## 4. Views (Blade Templates)

### Admin Billing Views
**Path**: `resources/views/admin/keuangan/tagihan/`

| File | Purpose | Key Components |
|------|---------|-----------------|
| **index.blade.php** | List all student bills | Filters (kelas, search, tahun), pagination, arrears summary |
| **show.blade.php** | Student bill details | Payment history, bill breakdown, print option |
| **edit.blade.php** | Edit bills form | Interactive input table, jenis_tagihan checkboxes, currency formatting |
| **bulk-create.blade.php** | Mass bill creation | Class/student selector, copy amounts, generated preview |
| **create-custom.blade.php** | Custom billing form | Custom type input, amount, due date |
| **generate-spp.blade.php** | SPP generation form | Month range selector, auto-generate 12 bills |
| **duplicate.blade.php** | Duplicate bills | Select previous year, modify amounts, bulk copy |
| **import.blade.php** | Excel import form | File uploader, template download link |
| **cetak-laporan.blade.php** | Print bill report | Date range, format options |

**Current File You're Editing**: [edit.blade.php](resources/views/admin/keuangan/tagihan/edit.blade.php)
- Provides interactive table for entering/modifying multiple bill types
- Uses JavaScript for currency formatting
- Mobile-responsive design

### Bendahara Billing Views
**Path**: `resources/views/bendahara/tagihan/`

**Same as Admin** + additional:
- **cetak.blade.php** - Individual bill print view

### Parent Billing Views
**Path**: `resources/views/orang-tua/tagihan/`

| File | Purpose |
|------|---------|
| **index.blade.php** | List child's outstanding bills with payment options |
| **invoice.blade.php** | Invoice for individual payment |

---

## 5. Data Flow Diagrams

### Creating/Updating Bills
```
User (Admin/Bendahara)
    ↓
    Edit Form (edit.blade.php)
    ↓
    TagihanController::update()
    ↓
    Database: Tagihan table
    ↓
    Status: 'belum_bayar' (unpaid)
```

### Payment Process
```
Parent/Student
    ↓
    orang-tua.tagihan.anak view
    ↓
    OrangTuaController::prosesBayar()
    ↓
    Midtrans Payment Gateway (snapPayment)
    ↓
    Payment Success/Callback
    ↓
    Pembayaran record created (status: pending)
    ↓
    MidtransWebhookController
    ↓
    Admin Validates Payment
    ↓
    Pembayaran: status_validasi = 'disetujui'
    ↓
    Tagihan::updateStatusBayar()
    ↓
    Tagihan: status updated (sudah_bayar/cicilan)
```

### Access Control Flow
```
Student attempts exam/rapor access
    ↓
    PengaturanBatasPembayaran check
    ↓
    Required jenis_tagihan defined?
    ↓
    Tagihan query:
    WHERE siswa_id = X
    AND jenis_tagihan IN (required_types)
    AND status != 'sudah_bayar'
    ↓
    If any unpaid → Deny access
    If all paid → Grant access
```

---

## 6. Key Features

### ✅ Bulk Operations
- **Bulk Create**: Create bills for entire class at once
- **Bulk Pay**: Pay multiple bills in single transaction
- **Bulk Import**: Load 100+ bills from Excel
- **Bulk Duplicate**: Copy previous year's bills

### ✅ Flexible Billing Types
- Pre-defined types (SPP, registration, uniform, etc.)
- Custom types (user-defined text)
- Monthly SPP auto-generation (12 bills)
- Labels customizable via `getLabelJenis()`

### ✅ Payment Tracking
- Status: unpaid → partial → paid → late
- Automatic calculation of remaining balance
- Double-payment prevention
- Payment history per student

### ✅ Reporting
- Individual invoices (PDF print)
- Bill reports with filters
- Payment history reports
- Arrears tracking

### ✅ Access Control
- Exam access gated by payment status
- Rapor access gated by payment
- Promotion validation based on bills
- Dispensation system for approved unpaid bills

### ✅ Integration
- Midtrans payment gateway
- Excel import/export
- Webhook auto-status updates
- Google Sheets sync

---

## 7. Current Issues & Notes

### From edit.blade.php
- Form shows jenis_tagihan as a table with rows and input fields
- Displays existing amounts for each billing type
- Note about SPP: "Untuk tagihan SPP Bulanan, gunakan Generate SPP"
- Mobile-responsive with custom styling

### Performance Considerations
- Index view uses `paginate(15)` to limit results
- Relationship eager-loading recommended for bulk queries
- API endpoints for filtering/preview (getSiswaByKelas, getTagihanPreview)

### Data Validation
- Amount fields: decimal(2)
- Due date: date format
- Status enum: limited values
- jenis_tagihan: flexible string or predefined

---

## 8. File Reference Summary

### Controllers
| File | Location | Role |
|------|----------|------|
| TagihanController (Base) | `Bendahara/` | Shared logic |
| TagihanController (Admin) | `Admin/Keuangan/` | Admin override |
| OrangTuaController | `OrangTua/` | Parent payment portal |
| PembayaranController | Both folders | Payment validation |

### Models
| File | Location | Purpose |
|------|----------|---------|
| Tagihan.php | `app/Models/` | Bill record |
| Pembayaran.php | `app/Models/` | Payment record |
| Siswa.php | `app/Models/` | Student (hasMany Tagihan) |
| TahunAjaran.php | `app/Models/` | Academic year (hasMany Tagihan) |

### Views
| Type | Location | Count |
|------|----------|-------|
| Admin | `resources/views/admin/keuangan/tagihan/` | 9 files |
| Bendahara | `resources/views/bendahara/tagihan/` | 9 files |
| OrangTua | `resources/views/orang-tua/tagihan/` | 2 files |

### Routes
| Scope | Prefix | File | Lines |
|-------|---------|------|-------|
| Admin | `/admin/keuangan/tagihan` | routes/web.php | 435-480 |
| Bendahara | `/bendahara/tagihan` | routes/web.php | 938-1025 |
| OrangTua | `/orang-tua/tagihan` | routes/web.php | 1460-1472 |

---

## 9. Usage Workflow Examples

### Example 1: Admin Creates Bills for New Class
```
1. Go to /admin/keuangan/tagihan/bulk-create
2. Select class and academic year
3. Define amounts for each jenis_tagihan
4. Click "Buat Tagihan Massal"
5. Bills created for all students in class
```

### Example 2: Parent Pays Child's Bills
```
1. Parent logs in as orang_tua role
2. Go to /orang-tua/dashboard
3. Click child's name under "Tagihan Anak"
4. See invoice view (/orang-tua/tagihan/anak/{siswa})
5. Select bills to pay
6. Click "Bayar" → Redirect to Midtrans gateway
7. Complete payment (post_auth if needed)
8. Return to app, payment pending validation
9. Admin validates at /admin/keuangan/pembayaran
10. Status updates to 'disetujui', bill marked paid
```

### Example 3: Generate Monthly SPP
```
1. Go to /admin/keuangan/tagihan/generate-spp
2. Select class and academic year
3. System auto-creates 12 bills:
   - spp_januari through spp_desember
   - Each with appropriate due date
   - Amount specified per type
4. View at /admin/keuangan/tagihan index
```

---

## 10. Testing Routes

A test task is available:
```bash
php artisan route:list --grep=google-sheets
```

To check billing routes:
```bash
php artisan route:list --grep=tagihan
php artisan route:list --grep=keuangan
```

---

## Summary Table: Who Does What

| Role | Can Create Bills | Can Validate Payment | Can Edit Bills | Can View Bills | Can Pay Bills |
|------|------------------|---------------------|-----------------|----------------|---------------|
| **Admin** | ✅ Yes | ✅ Yes | ✅ Yes | ✅ Yes | ❌ No |
| **Bendahara** | ✅ Yes | ✅ Yes | ✅ Yes | ✅ Yes | ❌ No |
| **Student** | ❌ No | ❌ No | ❌ No | ✅ View only | ❌ No |
| **Parent (Orang-tua)** | ❌ No | ❌ No | ❌ No | ✅ View own child | ✅ Yes (online payment) |

---

## Related Documentation
- Excel Import Template: `/admin/keuangan/tagihan/template`
- Payment Webhook: `MidtransWebhookController.php`
- Access Validation: `ValidasiAksesController.php`
- Google Sheets Sync: `GoogleSheetsController.php`
- Financial Audit Logs: `FinancialAuditLog.php` model
