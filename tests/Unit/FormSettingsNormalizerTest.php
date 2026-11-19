<?php

namespace Arqon\Forms\Tests\Unit;

use Arqon\Forms\Support\FormSettingsNormalizer;
use PHPUnit\Framework\TestCase;

class FormSettingsNormalizerTest extends TestCase
{
    public function test_it_removes_blank_values_and_empty_nodes_but_preserves_false_and_zero(): void
    {
        $settings = FormSettingsNormalizer::normalize([
            'page' => [
                'header_title' => '',
                'header_subtitle' => '   ',
                'footer_text' => null,
            ],
            'notification' => [
                'enabled' => false,
                'emails' => [],
            ],
            'availability' => [
                'submission_limit' => 0,
                'starts_at' => null,
            ],
        ]);

        self::assertSame([
            'notification' => [
                'enabled' => false,
            ],
            'availability' => [
                'submission_limit' => 0,
            ],
        ], $settings);
    }

    public function test_it_normalizes_comma_separated_notification_recipients(): void
    {
        $settings = FormSettingsNormalizer::normalize([
            'notification' => [
                'emails' => 'first@example.com, second@example.com',
            ],
        ]);

        self::assertSame([
            'notification' => [
                'emails' => ['first@example.com', 'second@example.com'],
            ],
        ], $settings);
    }
}
