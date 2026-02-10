<?php

namespace Tests\Acceptance\Enum\TopLevelWithDescription;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;

class TopLevelEnumWithDescriptionTest extends TestCase
{
    #[Test] public function top_level_enum_with_description_generates_comment(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                    ModelConfig::namespace => 'Tests\\generated',
                    ModelConfig::comments => true,
                ]
            ])
        );

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/RoleEnum.php',
            actualString: <<<PHP
                <?php
                namespace Tests\generated;
                /** A user role */
                enum RoleEnum: string
                {
                case admin = 'admin';
                case user = 'user';
                case guest = 'guest';
                }
                PHP
        );
    }
}
