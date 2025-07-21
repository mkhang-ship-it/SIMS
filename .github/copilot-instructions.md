# Tezonew - Club & Event Management System

## Architecture Overview

This is a Laravel 12 multi-tenant club management system with dual operational modes:

-   **Central Mode**: System administrators manage all clubs, users, and global settings
-   **Club Mode**: Club administrators manage their own members, events, and finances

### Dual Controller Pattern

Controllers are split into two hierarchies:

-   `app/Http/Controllers/*` - Central/admin controllers
-   `app/Http/Controllers/Club/*` - Club-specific controllers

Routes follow the same pattern with `backend.club.*` prefix for club operations.

## Permission System

**Critical**: All routes use the custom `CheckPermission` middleware with 3 parameters:

```php
middleware('CheckPermission:module_code,action,context')
// context: 'central' or 'branch'
```

User permissions are context-aware - users can have different roles in central vs. club contexts. Check `User->hasPermissionForModuleCode()` for implementation details.

### Club Context Switching

Users can belong to multiple clubs. Session-based club switching determines which club's data to show (`session('club_id')`).

### Database

Laravel migrations in `database/migrations/`. Prefix pattern:

-   `0001_01_01_*` - Core tables (clubs, users, majors)
-   `2025_*` - Feature tables (events, plans, invoices)

## Key Integrations

-   **Notifications**: `php-flasher/flasher-notyf-laravel`

## Code Conventions

### Model Relationships

Events use UUID primary keys (`HasUuids` trait). Most models have standard Laravel conventions but check for custom relationship methods.

### Form Handling

Controllers typically return JSON for AJAX requests and redirects for form submissions. Check `$request->expectsJson()` patterns.

### File Structure

-   Views: Standard Blade templates in `resources/views/`
-   Assets: Images in `public/assets/images/`, compiled CSS/JS in `public/assets/`

## Common Tasks

### Adding New Features

1. Create migration for new tables
2. Add model with appropriate relationships
3. Create controller (choose central vs club hierarchy)
4. Add routes with proper `CheckPermission` middleware
5. Create Blade views following existing patterns

### Permission Management

Permissions are module-based. Check `database/migrations/*permissions*` for the permission structure. Always verify context (`central`/`branch`) matches the intended access level.
