<?php

namespace Tests\Acceptance\Properties\Required\Nullable;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;
use Zerotoprod\DataModelGenerator\Models\PropertyConfig;

class RequiredNullableTest extends TestCase
{
    #[Test] public function required_property_gets_required_attribute_and_non_required_gets_default_null(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                    ModelConfig::properties => [
                        PropertyConfig::nullable => true,
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
                public string \$name;
                public string|null \$age = null;
                }
                PHP
        );
    }
}
