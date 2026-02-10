<?php

namespace Tests\Acceptance\Enum\TopLevel;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;

class TopLevelEnumTest extends TestCase
{
    #[Test] public function top_level_enum_generates_enum_file(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                    ModelConfig::namespace => 'Tests\\generated',
                ]
            ])
        );

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/RoleEnum.php',
            actualString: <<<PHP
                <?php
                namespace Tests\generated;
                enum RoleEnum: string
                {
                case admin = 'admin';
                case user = 'user';
                case guest = 'guest';
                }
                PHP
        );
    }

    #[Test] public function top_level_enum_does_not_generate_model(): void
    {
        $components = OpenApi30::adapt(
            json_decode(file_get_contents(__DIR__.'/schema.json'), true)
        );

        self::assertEmpty($components->Models);
        self::assertNotEmpty($components->Enums);
    }
}
