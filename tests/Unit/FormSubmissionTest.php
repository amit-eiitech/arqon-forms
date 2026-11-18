<?php

namespace Arqon\Forms\Tests\Unit;

use Arqon\Forms\Models\FormSubmission;
use Orchestra\Testbench\TestCase;

class FormSubmissionTest extends TestCase
{
    public function test_option_values_are_displayed_as_labels(): void
    {
        $submission = new FormSubmission;
        $submission->data = [
            'country' => 'option_country',
            'interests' => ['option_music', 'other'],
        ];

        $options = [
            ['label' => 'Canada', 'value' => 'option_country'],
            ['label' => 'Music', 'value' => 'option_music'],
        ];

        self::assertSame('Canada', $submission->valueForField([
            'name' => 'country',
            'options' => $options,
        ]));
        self::assertSame(['Music', 'other'], $submission->valueForField([
            'name' => 'interests',
            'options' => $options,
        ]));
    }
}
