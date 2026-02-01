# Temporary Debug Files - Safe to Delete

This directory contains temporary files created during development and debugging.
These files are NOT needed for the application to run and can be safely deleted.

## Files to Delete:

### Password Debug Files
- `admin_hash.txt` - Temporary password hash
- `user_hash.txt` - Temporary password hash
- `password_hashes.txt` - Debug output
- `production_hashes.txt` - Debug output
- `check_hash.php` - Password verification script
- `debug_passwords.php` - Password debug script
- `fix_passwords.php` - Local password fix script
- `fix_passwords_production.sql` - SQL fix file
- `fix_production_passwords.php` - Production password fix script
- `generate_hashes.php` - Hash generation script
- `get_production_hashes.php` - Production hash retrieval
- `get_seed_hashes.php` - Seed hash generation
- `verify_seed_hashes.php` - Seed verification script
- `FINAL_PASSWORD_FIX.sql` - Final password fix SQL

### Login Debug Files
- `debug_login.php` - Login debugging script

### Email Debug Files
- `email_preview.php` - Email template preview
- `test_sendgrid.php` - SendGrid test script
- `test_sendgrid_debug.php` - SendGrid debug script
- `sendgrid_test_result.txt` - Test output

## How to Clean Up

### Option 1: Manual Deletion
Delete the files listed above manually.

### Option 2: Run Cleanup Script
```bash
php scripts/cleanup_temp_files.php
```

### Option 3: Git Clean
```bash
# Remove untracked files (be careful!)
git clean -n  # Preview what will be deleted
git clean -f  # Actually delete
```

## Files to KEEP

These files are important for the application:
- `.env` - Your environment configuration (already in .gitignore)
- `.env.example` - Template for environment variables
- `composer.json` / `composer.lock` - PHP dependencies
- `docker-compose.yml` / `Dockerfile` - Docker configuration
- `render.yaml` - Render deployment configuration
- `README.md` - Project documentation
- All files in `admin/`, `user/`, `api/`, `includes/`, `config/`, `database/`, `assets/`

## Note

The `.gitignore` file already excludes most of these temporary files from version control,
so they won't be committed to your repository.
