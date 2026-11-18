# Arqon Forms

A lightweight, modern dynamic form builder and submission manager for **Laravel 13** and **Filament 5**.

Package: `eii/arqon-forms`  
Publisher: Eii Tech Solutions

---

## Features

- **Filament 5 Form Management:** Intuitive builder resource for managing dynamic forms.
- **In-Panel Modal Preview:** Instant preview of forms inside a Filament modal with zero tab switching or URL redirects.
- **Tailwind CSS v4 Powered:** Beautiful, responsive UI compiled with Tailwind CSS v4 and zero CSS reset bleed into Filament.
- **Light & Dark Mode:** Native dark mode styling that automatically synchronizes with Filament's theme.
- **Reorderable JSON Field Builder:** Store flexible form schemas that adapt easily without schema migrations.
- **Livewire-Powered Forms:** Reactive input handling, honeypot protection, and instant validation.
- **Reusable Blade Component:** Easily embed any form with `<x-arqon-form slug="contact-us" />`.
- **Public & Standalone Pages:** Render public form endpoints at `/forms/{slug}` standalone or embedded in your app layout.
- **Native Submissions Management:** View and manage responses using Filament Infolists and structured data snapshots.
- **Rate Limiting & Security:** Built-in per-IP rate limiting, honeypot field, and HMAC IP address hashing.
- **Email Notifications:** Configurable submission alerts delivered synchronously or via queued background jobs.

---

## Requirements

- **PHP:** 8.3+
- **Laravel:** 13.x
- **Filament:** 5.x
- **Livewire:** 4.x

---

## Installation

Install the package via Composer:

```bash
composer require eii/arqon-forms:^1.0
```

Publish the package configuration:

```bash
php artisan vendor:publish --tag=arqon-forms-config
```

Run the database migrations:

```bash
php artisan migrate
```

Register the plugin in your Filament panel provider (usually `app/Providers/Filament/AdminPanelProvider.php`):

```php
use Arqon\Forms\Filament\ArqonFormsPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...your existing panel configuration
        ->plugins([
            ArqonFormsPlugin::make(),
        ]);
}
```

---

## Creating & Publishing Forms

1. Navigate to **Forms → Forms** in your Filament panel.
2. Click **New Form** and add your fields using the visual builder.
3. Use clean snake_case field identifiers (e.g., `full_name`, `company_email`).
4. Toggle **Published** to make the form active, then save.
5. Your form is now accessible via `/forms/{slug}` or ready to embed in any page.

---

## Form Preview

Arqon Forms includes an **In-Panel Modal Preview**:

- Clicking the **Preview** action on any form row or edit page opens a modal showing the live form.
- The preview runs the real Livewire component with submissions disabled to prevent test pollution.
- The modal matches your Filament panel theme with zero style bleed, honoring light and dark mode automatically.
- Optional temporary signed preview URLs (`/forms/_preview/{id}`) remain available for external reviewers if enabled in config.

---

## Embedding in Blade Templates

Embed any published form anywhere in your Laravel templates using the custom Blade component:

```blade
<x-arqon-form slug="contact-us" />
```

Ensure Livewire scripts and styles are present in your layout (`@livewireStyles` / `@livewireScripts`).

---

## Rendering Inside Your Website Layout

By default, `/forms/{slug}` renders using an internal, self-contained standalone layout with precompiled Tailwind CSS.

To render public forms inside your website's main layout, configure `render.layout` and `render.section` in `config/arqon-forms.php`:

```php
'render' => [
    'layout' => 'layouts.app',
    'section' => 'content',
],
```

When a custom layout is configured, the form seamlessly adopts your application shell while maintaining its own input styling.

---

## Supported Field Types

| Field Type | Key | Description |
|---|---|---|
| Short answer | `text` | Single-line text input |
| Paragraph | `textarea` | Multi-line text input |
| Email | `email` | Email validation |
| Number | `number` | Numeric input |
| Phone | `tel` | Telephone input |
| Website URL | `url` | Web link validation |
| Dropdown | `select` | Single selection dropdown with custom options |
| Single choice | `radio` | Radio button list |
| Multiple choice | `checkbox` | Multi-select checkboxes |
| Date | `date` | HTML5 date picker |
| Yes / No | `toggle` | Toggle / boolean checkbox |
| Hidden | `hidden` | Stored form payload without respondent interaction |

---

## Customizing Views

To customize the frontend templates, publish the package views:

```bash
php artisan vendor:publish --tag=arqon-forms-views
```

To keep your project clean, this publishes only the essential templates:

1. `resources/views/vendor/arqon-forms/livewire/public-form.blade.php`: The interactive form inputs, submit button, and error states.
2. `resources/views/vendor/arqon-forms/public.blade.php`: The public form wrapper page (card container, headers, and footers).
3. `resources/views/vendor/arqon-forms/emails/submission-notification.blade.php`: Email notification template.

---

## Styling & Asset Building

Arqon Forms uses **Tailwind CSS v4** with a CSS-first setup in `resources/css/arqon-forms.css`:

```bash
# Build minified CSS for distribution
npm run build

# Watch for CSS and template changes during development
npm run dev
```

The compiled styles are placed in `dist/arqon-forms.css` and are automatically embedded in standalone and preview layouts without requiring separate asset publishing.

---

## Configuration Reference

Key settings in `config/arqon-forms.php`:

| Setting | Default | Description |
|---|---|---|
| `route.prefix` | `'forms'` | URL prefix for public form endpoints |
| `route.middleware` | `['web']` | Middleware applied to form routes |
| `preview.enabled` | `true` | Enables temporary signed preview endpoints |
| `preview.allow_submissions` | `false` | Whether submissions are allowed during preview |
| `submission.rate_limit.max_attempts` | `5` | Maximum attempts per IP per decay window |
| `submission.rate_limit.decay_minutes` | `1` | Decay window in minutes for rate limiting |
| `submission.store_ip_hash` | `true` | Store salted HMAC IP hashes rather than raw IPs |
| `notifications.delivery` | `'sync'` | Deliver notification emails via `'sync'` or `'queue'` |

For production, set a dedicated IP hash secret in `.env`:

```dotenv
ARQON_FORMS_IP_HASH_KEY="a-long-random-secret-value"
```

To queue notifications asynchronously:

```dotenv
ARQON_FORMS_NOTIFICATION_DELIVERY=queue
ARQON_FORMS_NOTIFICATION_QUEUE_CONNECTION=redis
ARQON_FORMS_NOTIFICATION_QUEUE=default
ARQON_FORMS_NOTIFICATION_QUEUE_DELAY_SECONDS=0
```

---

## Testing

Run the test suite:

```bash
composer test
```

Code formatting:

```bash
composer format
```

---

## License

MIT. See [LICENSE](LICENSE).
