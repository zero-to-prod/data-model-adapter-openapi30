<?php

namespace Tests\Unit\VersionValidation\Correct;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Models\Components;

class AdaptTest extends TestCase
{
    #[Test] public function correct_version_validation(): void
    {
        self::assertTrue(
            is_a(
                object_or_class: OpenApi30::adapt(
                    json_decode(file_get_contents(__DIR__.'/schema.json'), true),
                ),
                class: Components::class
            )
        );
    }
}