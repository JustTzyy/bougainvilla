# Guest Cleanup System

This system automatically manages guest data retention by implementing a two-phase deletion process:

## Overview

- **Phase 1 (Soft Delete)**: Guests older than 3 months are soft deleted
- **Phase 2 (Hard Delete)**: Guests that have been soft deleted for 3+ months are permanently deleted
- **Total Retention**: 6 months maximum

## Components

### 1. Artisan Command
```bash
php artisan guests:cleanup
php artisan guests:cleanup --dry-run  # Preview without executing
```

### 2. Scheduled Task
- Runs daily at 2:00 AM
- Configured in `ScheduleServiceProvider`
- Uses Laravel's task scheduler

### 3. Admin Interface
- Access via: `/adminPages/cleanup`
- View statistics and manage cleanup
- Manual cleanup execution
- Individual guest management

## Database Changes

### Migration: `add_cleanup_tracking_to_guests_table`
- `last_cleanup_check` - Timestamp of last cleanup check
- `cleanup_notified` - Boolean flag for notifications

## Usage

### Automatic Cleanup
The system runs automatically via Laravel's scheduler. To enable:
```bash
# Add to crontab (Linux/Mac)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

# Or use Windows Task Scheduler for Windows
```

### Manual Cleanup
1. Go to Admin Dashboard → Reports → Guest Cleanup
2. Click "Run Cleanup Now" for immediate execution
3. Click "Preview Cleanup" to see what would be deleted

### Individual Management
- **Soft Delete**: Remove individual guests from active records
- **Hard Delete**: Permanently remove soft-deleted guests

## Safety Features

- **Dry Run Mode**: Preview changes before execution
- **Confirmation Dialogs**: Prevent accidental deletions
- **Detailed Logging**: Track all cleanup activities
- **Graceful Error Handling**: System continues on individual failures

## Configuration

### Timing
- Soft Delete: 3 months after creation
- Hard Delete: 3 months after soft deletion
- Schedule: Daily at 2:00 AM

### Customization
Edit `app/Console/Commands/CleanupGuestsCommand.php` to modify:
- Time periods
- Deletion logic
- Output format

## Monitoring

### Statistics Dashboard
- Total guests count
- Active vs soft-deleted counts
- Guests ready for each phase

### Command Output
- Detailed processing information
- Error reporting
- Summary statistics

## Troubleshooting

### Command Not Running
1. Check if scheduler is enabled
2. Verify cron job is set up
3. Check Laravel logs for errors

### Permission Issues
1. Ensure web server can execute Artisan commands
2. Check file permissions
3. Verify database access

### Data Recovery
- Soft-deleted guests can be restored using `restore()` method
- Hard-deleted guests cannot be recovered
- Always backup before running cleanup

## Security Considerations

- Only admin users can access cleanup interface
- All actions are logged
- Confirmation required for destructive operations
- Dry run mode prevents accidental deletions
