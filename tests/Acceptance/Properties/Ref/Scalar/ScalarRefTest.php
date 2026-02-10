<?php

namespace Tests\Acceptance\Properties\Ref\Scalar;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;
use Zerotoprod\DataModelGenerator\Models\PropertyConfig;

class ScalarRefTest extends TestCase
{
    #[Test] public function ref_to_scalar_resolves_to_primitive_type(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                    ModelConfig::properties => [
                        PropertyConfig::readonly => true
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
                public readonly integer \$created_at;
                }
                PHP
        );
    }

    #[Test] public function ref_to_scalar_includes_description_from_referenced_schema(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                    ModelConfig::properties => [
                        PropertyConfig::readonly => true,
                        PropertyConfig::comments => true,
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
                /** Milliseconds since the unix epoch */
                public readonly integer \$created_at;
                }
                PHP
        );
    }

    #[Test] public function ref_to_scalar_does_not_generate_class(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                ]
            ])
        );

        self::assertFileDoesNotExist(self::$test_dir.'/ZonedDateTime.php');
    }
}
