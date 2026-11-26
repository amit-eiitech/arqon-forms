<?php

namespace Arqon\Forms\Traits;

use BackedEnum;
use Filament\Support\Icons\Heroicon;

trait HasPackageIcons
{
    /**
     * Resolve a package icon from configuration or fallback to defaults.
     *
     * @param  string  $name  The semantic icon key.
     */
    public static function packageIcon(
        string $name
    ): string|BackedEnum|null {
        return config(
            "arqon-forms.icons.{$name}",
            static::defaultPackageIcons()[$name] ?? null
        );
    }

    /**
     * Define the default icons used by the package.
     *
     * @return array<string, string|BackedEnum|null>
     */
    public static function defaultPackageIcons(): array
    {
        return [
            // Resource navigation icons
            'form' => Heroicon::OutlinedDocumentText,
            'submissions' => Heroicon::OutlinedInbox,
            'settings' => Heroicon::OutlinedCog6Tooth,

            // General action icons
            'create' => Heroicon::OutlinedPlus,
            'edit' => Heroicon::OutlinedPencilSquare,
            'delete' => Heroicon::OutlinedTrash,
            'view' => Heroicon::OutlinedEye,

            // Form action icons
            'preview' => Heroicon::OutlinedEye,
            'open_public_form' => Heroicon::OutlinedArrowTopRightOnSquare,

            // Submission action icons
            'mark_as_read' => Heroicon::OutlinedCheck,
            'mark_as_replied' => Heroicon::OutlinedArrowUpRight,
            'archive' => Heroicon::OutlinedArchiveBox,
            'export_csv' => Heroicon::OutlinedArrowDownTray,
        ];
    }
}
