# Changelog

## 1.0.2 - 2026-11-26

- Added configurable resource navigation and action icons via `HasPackageIcons` trait.
- Action icons (`create`, `edit`, `delete`, `view`, `preview`, `open_public_form`, `mark_as_read`, `mark_as_replied`, `archive`, `export_csv`) can now be customized via config or trait.
- Replaced temporary preview route and configuration with in-panel modal preview.

## 1.0.1 - 2026-11-26

- Form publishing and submission workflow improvements.

## 1.0.0 - 2026-11-17

- Initial Laravel 13 / Filament 5 release.
- Form and submission Eloquent models and migrations.
- Filament form and submission resources.
- Reorderable form field editor.
- Public Livewire form renderer and Blade component.
- Dynamic server-side validation, honeypot and rate limiting.
- Email notifications and CSV export.
