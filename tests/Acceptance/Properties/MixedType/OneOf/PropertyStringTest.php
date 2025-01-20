<?php

namespace Tests\Acceptance\Properties\MixedType\OneOf;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;
use Zerotoprod\DataModelGenerator\Models\PropertyConfig;
use Zerotoprod\DataModelGenerator\Models\Type;

class PropertyStringTest extends TestCase
{
    #[Test] public function generate(): void
    {
        Engine::generate(
            OpenApi30::adapt(file_get_contents(__DIR__.'/openapi30.json')),
            Config::from([
                Config::directory => self::$test_dir,
                Config::model => [
                    ModelConfig::properties => [
                        PropertyConfig::comments => true,
                        PropertyConfig::types => [
                            'integer' => 'string',
                            'number' => 'int'
                        ]
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
                public string|int \$age;
                }
                PHP
        );
    }
}