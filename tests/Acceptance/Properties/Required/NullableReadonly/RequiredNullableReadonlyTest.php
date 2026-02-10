<?php

namespace Tests\Acceptance\Properties\Required\NullableReadonly;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;
use Zerotoprod\DataModelGenerator\Models\PropertyConfig;

class RequiredNullableReadonlyTest extends TestCase
{
    #[Test] public function required_readonly_gets_required_attribute_and_non_required_readonly_gets_nullable_attribute(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                    ModelConfig::properties => [
                        PropertyConfig::nullable => true,
                        PropertyConfig::readonly => true,
                    ]
                ]
            ])
        );

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/User.php',
            actualString: <<<PHP
                <?php
                class User
                {
                #[\Zerotoprod\DataModel\Describe(['required' => true])]
                public readonly string \$name;
                #[\Zerotoprod\DataModel\Describe(['nullable'])]
                public readonly string|null \$age;
                }
                PHP
        );
    }
}
