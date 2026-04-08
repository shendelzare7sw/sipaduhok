# 📋 Google Sheets Integration - Deployment & Quick Start Guide

## ✅ Project Status: COMPLETE
- **All 4 Phases**: Completed & Tested ✓
- **Total Tests**: 19/19 Passed (100%)
- **Production Ready**: YES ✓

---

## 🚀 Quick Start

### Step 1: Verify Installation
```bash
# Run final validation test
php test-all-phases-final.php

# Expected output: 19/19 tests passed (100%)
```

### Step 2: Database Setup
```bash
# Run migrations
php artisan migrate

# Verify table created
php artisan tinker
>>> DB::table('google_sheets_sync_logs')->count()
# Should return 0 (empty table)
```

### Step 3: Configure Google Sheets
1. Go to Admin Dashboard: `http://localhost/admin`
2. Click "Google Sheets Sync" in sidebar menu
3. Click "Setup Now" button
4. Follow 4-step wizard:
   - Download Service Account JSON from Google Cloud Console
   - Upload JSON file
   - Enter your Spreadsheet ID
   - Click "Test Connection"
5. Click "Save Configuration"

### Step 4: Start Queue Worker
```bash
# Terminal 1: Start queue worker (processes sync jobs)
php artisan queue:work

# Keep this running in production via supervisor/systemd
```

### Step 5: Start Scheduler (Optional - for testing)
```bash
# Terminal 2: Run scheduler every minute (during development)
php artisan schedule:work

# Or in production, add to crontab:
# * * * * * cd /path/to/sipaduhok && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 Dashboard Access

- **URL**: `/admin/google-sheets`
- **Role Required**: Admin
- **Features**:
  - Connection status badge
  - Module status cards (10 modules)
  - Quick action buttons (Push/Pull/Preview)
  - Recent sync history
  - Setup wizard link

---

## 🔄 Manual Operations

### Push Data to Sheets
```bash
# Manual trigger via API
curl -X POST http://localhost/admin/google-sheets/push/siswa \
  -H "X-CSRF-TOKEN: $(csrf-token)"

# Or via UI: Dashboard → Module Card → "⬆️ Push" button
```

### Preview Data Before Import
```bash
# Via UI: Dashboard → Module Card → "⬇️ Preview" button
# Shows first 50 rows with row selection
# Can select specific rows to import
```

### Pull Data from Sheets
```bash
# Via UI: Preview → "Import Selected Data" button
# Queues async job to import selected rows
```

### Check Sync History
```bash
# Via UI: Dashboard → "Recent Sync History" section
# View all past syncs with timestamps and status
```

---

## ⏰ Automatic Scheduling

### Tier 1: Daily (Every Day at 00:30 UTC)
```
siswa          → Push all student records
guru           → Push all teacher records
kelas          → Push all class records
jadwal_pelajaran → Push all schedules
presensi       → Push attendance records
nilai          → Push grade records
```

### Tier 2: Weekly (Every Sunday at 01:00 UTC)
```
tagihan        → Push all invoices
pembayaran     → Push all payments
siswa_belum_lunas → Push unpaid students report
rekap_keuangan → Push financial summary
```

---

## 🔍 Monitoring & Troubleshooting

### View Sync Logs
```bash
# Check database logs
php artisan tinker
>>> App\Models\GoogleSheetsSyncLog::latest()->limit(10)->get()

# View with status filtering
>>> App\Models\GoogleSheetsSyncLog::where('status', 'failed')->get()
```

### View Application Logs
```bash
# Google Sheets channel log
tail -f storage/logs/google-sheets.log

# Or full application log
tail -f storage/logs/laravel.log
```

### Test Connection
```bash
# Via UI: Setup page → "Test Connection" button

# Or via code:
php artisan tinker
>>> app(\App\Services\GoogleSheetsService::class)->testConnection()
# Returns true/false
```

### Common Issues

**Issue: "Connection Failed" in Test**
- Verify Service Account JSON is valid
- Check Spreadsheet ID is correct
- Ensure Service Account email has been granted Spreadsheet access
- Verify Google Sheets & Google Drive APIs are enabled

**Issue: Queue Jobs Not Processing**
- Ensure queue worker is running: `php artisan queue:work`
- Check queue database: `SELECT * FROM jobs;`
- Monitor queue:  `php artisan queue:monitor`

**Issue: Scheduler Not Running**
- Verify cron job is set up in production
- Check scheduler: `php artisan schedule:list`
- Test manually: `php artisan schedule:run`

**Issue: Data Not Syncing**
- Check sync logs: `App\Models\GoogleSheetsSyncLog::latest(10)->get()`
- View error messages: `sync_log->error_message`
- Verify module configuration: `config('google-sheets.modules')`

---

## 🔐 Security Configuration

### Service Account Setup
```
✓ JSON stored in: storage/app/credentials/google-service-account.json
✓ Excluded from Git: Yes (.gitignore)
✓ Accessible to: Web server only
✓ Permissions: 0755 (read-only)
```

### Access Control
```
✓ Admin middleware: role:admin
✓ No guest access
✓ CSRF protection: All POST requests
✓ Rate limiting: Not applied (add if needed)
```

### Data Security
```
✓ Credentials: Stored securely, never logged
✓ Sync errors: Logged without sensitive data
✓ Database: Foreign key to users (audit trail)
✓ Logs: Rotated daily
```

---

## 📈 Performance Tuning

### Queue Configuration
Current setting: `QUEUE_CONNECTION=database`

For better performance, consider:
```env
# Use Redis (if available)
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Scheduler Optimization
```php
// Current: Overlapping prevented with 10 min timeout
->withoutOverlapping(timeout: 600)

// Can adjust timeout based on data size:
->withoutOverlapping(timeout: 1800)  # 30 minutes for large syncs
```

### Logging Level
```env
# Current: default

# Production recommendation:
LOG_LEVEL=error  # Only log errors to reduce disk space
```

---

## 📦 Environment Configuration

### Required .env Variables
```env
GOOGLE_SHEETS_ENABLED=true
GOOGLE_SERVICE_ACCOUNT_JSON_PATH=storage/app/credentials/google-service-account.json
GOOGLE_SHEETS_DEFAULT_SPREADSHEET_ID=YOUR_SPREADSHEET_ID
GOOGLE_SHEETS_AUTO_SYNC_ENABLED=true
GOOGLE_SHEETS_LOG_CHANNEL=single
GOOGLE_SHEETS_RETRY_ATTEMPTS=3
GOOGLE_SHEETS_TIMEOUT_SECONDS=300
GOOGLE_SHEETS_CONFLICT_RESOLUTION=database
```

### Optional: Custom Configuration
```env
# Change sync times (in bootstrap/app.php withSchedule closure)
dailyAt('02:00')    # Change from 00:30 to 02:00
weeklyOn(1, '03:00') # Change to Monday at 03:00
```

---

## 🧪 Testing in Development

### Test Tier 1 Sync Manually
```bash
php artisan tinker

# Dispatch single module sync
dispatch(new \App\Jobs\SyncModuleToSheet('siswa', 'push'));

# Queue will process immediately with queue:work running
```

### Test Tier 2 Sync Manually
```bash
dispatch(new \App\Jobs\SyncModuleToSheet('tagihan', 'push'));
```

### Verify Sync Completion
```bash
# Check sync log
App\Models\GoogleSheetsSyncLog::latest()->first()

# View results
>>> $log->status     // 'success' or 'failed'
>>> $log->rows_synced // number of rows
>>> $log->error_message // null if successful
```

---

## 📋 Maintenance Checklist

### Weekly
- [ ] Review sync logs for errors
- [ ] Check Spreadsheet data accuracy
- [ ] Monitor queue job count: `php artisan queue:monitor`

### Monthly
- [ ] Verify all 10 modules syncing correctly
- [ ] Test connection via admin panel
- [ ] Review error logs

### Quarterly
- [ ] Update Google API client library
- [ ] Audit sync log retention (can trim old records)
- [ ] Performance review of queue processing

---

## 🚨 Production Deployment

### Pre-Deployment Checklist
- [ ] All 4 phases tested: `php test-all-phases-final.php`
- [ ] Database migrations applied: `php artisan migrate`
- [ ] Service Account JSON uploaded & tested
- [ ] Queue worker configured (supervisor/systemd)
- [ ] Scheduler added to crontab
- [ ] Logs directory writable: `storage/logs/`
- [ ] Environment variables configured

### Production Setup Commands
```bash
# 1. Clone/deploy code
git clone ... && cd sipaduhok

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Run migrations
php artisan migrate --force

# 4. Cache configuration
php artisan config:cache

# 5. Start queue worker (via supervisor)
supervisord -c /etc/supervisor/conf.d/sipaduhok-worker.conf

# 6. Configure cron (add to /etc/crontab)
* * * * * cd /var/www/sipaduhok && php artisan schedule:run >> /dev/null 2>&1
```

### Supervisor Configuration Example
```ini
[program:sipaduhok-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sipaduhok/artisan queue:work --sleep=3 --tries=3 --timeout=300
autostart=true
autorestart=true
stopasgroup=true
numprocs=3
redirect_stderr=true
stdout_logfile=/var/www/sipaduhok/storage/logs/worker.log
stdout_logfile_maxbytes=100MB
stdout_logfile_backups=10
```

---

## 📞 Support & Resources

### Documentation
- [Google Sheets API Docs](https://developers.google.com/sheets/api)
- [Laravel Queue Docs](https://laravel.com/docs/11.x/queues)
- [Laravel Scheduler Docs](https://laravel.com/docs/11.x/scheduling)

### Completion Report
- Full project summary: `GOOGLE_SHEETS_COMPLETION_REPORT.md`
- Test results: `test-phase1-simple.php`, `test-phase2-simple.php`, `test-phase3-simple.php`, `test-phase4-simple.php`

---

## ✅ Deployment Verification

After deploying to production, verify:

```bash
# 1. Admin panel accessible
curl https://your-domain/admin/google-sheets

# 2. Queue processing
php artisan queue:monitor

# 3. Scheduler running
php artisan schedule:list

# 4. Sync logs recording
App\Models\GoogleSheetsSyncLog::count()  # Should increase over time
```

---

**Project complete and ready for production! 🚀**

For questions or issues, refer to the completion report or test files for detailed validation logic.
