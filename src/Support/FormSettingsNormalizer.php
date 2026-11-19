<?php

namespace Arqon\Forms\Support;

class FormSettingsNormalizer
{
    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public static function normalize(array $settings): array
    {
        $rawEmails = data_get($settings, 'notification.emails', []);
        $emails = is_array($rawEmails)
            ? $rawEmails
            : preg_split('/[,;\s]+/', (string) $rawEmails, -1, PREG_SPLIT_NO_EMPTY);

        data_set($settings, 'notification.emails', $emails ?: []);

        return self::removeBlankValues($settings);
    }

    /**
     * @param  array<array-key, mixed>  $values
     * @return array<array-key, mixed>
     */
    private static function removeBlankValues(array $values): array
    {
        foreach ($values as $key => $value) {
            if ($value === null || (is_string($value) && trim($value) === '')) {
                unset($values[$key]);

                continue;
            }

            if (is_array($value)) {
                $value = self::removeBlankValues($value);

                if ($value === []) {
                    unset($values[$key]);

                    continue;
                }

                $values[$key] = $value;
            }
        }

        return $values;
    }
}
