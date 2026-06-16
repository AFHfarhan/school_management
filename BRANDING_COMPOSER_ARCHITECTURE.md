# Runtime Branding Standardization — BrandingComposer Architecture

## Overview

The `BrandingComposer` centralizes school identity and branding data for all layout and theme views. It resolves branding values from the `ComponentService` at render time, with sensible fallbacks for missing configuration.

## Architecture

### Data Flow

```
ComponentService (app/Services/ComponentService.php)
    ↓
BrandingComposer (app/View/Composers/BrandingComposer.php)
    ↓
View Payload ($branding array)
    ↓
Blade Templates (layouts/*, themes/*)
```

### Binding

Registered in `AppServiceProvider::boot()`:

```php
View::composer([
    'layouts.*',
    'themes.*',
], BrandingComposer::class);
```

This automatically injects a `$branding` variable into all layout and theme views.

## Available Branding Data

The `$branding` array includes:

| Key | Source | Fallback | Purpose |
|---|---|---|---|
| `school_name` | ComponentService::getSchoolProfileValue('school_name') | `'SMK SUKAMAKMUR'` | Primary school identifier in sidebar/footer |
| `school_tagline` | ComponentService::getSchoolProfileValue('school_tagline') | `'Quality Education'` | Descriptive tagline / motto |
| `logo` | ComponentService::getBrandingValue('logo') | asset path to favicon | Logo image URL for branding surfaces |
| `favicon` | ComponentService::getBrandingValue('favicon') | asset path to favicon | Favicon URL for browser tab |
| `address` | ComponentService::getSchoolProfileValue('address') | `'Main Street'` | School address for contact info |
| `phone` | ComponentService::getSchoolProfileValue('phone') | `'0000000000'` | School phone number |
| `email` | ComponentService::getSchoolProfileValue('email') | `'school@example.com'` | School email address |

## Usage in Blade

### Access via array syntax (current pattern)

```blade
<!-- Display school name -->
<h1>{{ $branding['school_name'] }}</h1>

<!-- Use favicon -->
<link rel="icon" href="{{ $branding['favicon'] }}">

<!-- Footer with year -->
<footer>
    <p>© {{ $branding['school_name'] }} {{ now()->year }}</p>
</footer>
```

### Access via object syntax (optional, requires helper)

If you want a cleaner interface, you can cast the array to an object:

```blade
@php($branding = (object) $branding)

<!-- Now you can use dot notation -->
<h1>{{ $branding->school_name }}</h1>
```

## Fallback Strategy

If a branding value is not configured in the ComponentService, the composer will use the fallback value. This ensures:

- **No broken views** — if the database is empty or ComponentService throws an error, Blade still renders with sensible defaults.
- **Smooth migration** — you can deploy the composer without updating the database immediately.
- **Predictable behavior** — all fallback values are transparent and documented.

## Extending the Composer

To add more branding fields:

1. Update the `$branding` array in `BrandingComposer::compose()`.
2. Add a corresponding entry in the ComponentService (if needed).
3. Update the seeder to include the new default value.
4. Document the new key in the table above.

## Current State

- ✅ BrandingComposer created and registered
- ❌ Blade views NOT yet migrated (planned for Sprint 2)
- 🔄 Views currently use inline `app(ComponentService::class)` calls; these will be replaced by `$branding` once migration is complete

## Next Steps

Once you're ready to migrate views:

1. Replace inline service lookups with `$branding` array access.
2. Remove redundant `app(ComponentService::class)` calls from Blade files.
3. Add new branding fields to the ComponentSeeder as needed.
4. Verify that all layouts and themes render correctly with the centralized branding payload.

## Example: Before and After

### Before (current inline approach)
```blade
@php
    $componentService = app(\App\Services\ComponentService::class);
    $schoolName = $componentService->getSchoolProfileValue('school_name', 'SMK SUKAMAKMUR');
@endphp
<div>{{ $schoolName }}</div>
```

### After (using BrandingComposer)
```blade
<div>{{ $branding['school_name'] }}</div>
```

---

**Document Version:** 1.0  
**Date Created:** 2026-06-16  
**Maintainer:** Platform Architecture Team
