<?php

namespace Tests\Acceptance\Enum\RefToEnum;

use PHPUnit\Framework\Attributes\Test;
use Tests\generated\RoleEnum;
use Tests\generated\User;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\ConstantConfig;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;
use Zerotoprod\DataModelGenerator\Models\PropertyConfig;

class RefToEnumTest extends TestCase
{
    #[Test] public function ref_to_enum_uses_enum_suffix_in_type(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                    ModelConfig::namespace => 'Tests\\generated',
                    ModelConfig::properties => [
                        PropertyConfig::readonly => true,
                    ],
                ]
            ])
        );

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/User.php',
            actualString: <<<PHP
                <?php
                namespace Tests\generated;
                class User
                {
                public readonly RoleEnum \$role;
                }
                PHP
        );
    }

    #[Test] public function ref_to_enum_generates_both_files(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                    ModelConfig::namespace => 'Tests\\generated',
                    ModelConfig::constants => [
                        ConstantConfig::comments => true,
                    ],
                    ModelConfig::properties => [
                        PropertyConfig::readonly => true,
                    ],
                    ModelConfig::use_statements => [
                        'use \\Zerotoprod\\DataModel\\DataModel;'
                    ],
                ]
            ])
        );

        $user = User::from([
            User::role => RoleEnum::admin,
        ]);

        self::assertEquals(RoleEnum::admin, $user->role);
    }
}
