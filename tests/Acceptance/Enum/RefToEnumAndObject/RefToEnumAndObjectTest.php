<?php

namespace Tests\Acceptance\Enum\RefToEnumAndObject;

use PHPUnit\Framework\Attributes\Test;
use Tests\generated\Address;
use Tests\generated\RoleEnum;
use Tests\generated\User;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\ConstantConfig;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;
use Zerotoprod\DataModelGenerator\Models\PropertyConfig;

class RefToEnumAndObjectTest extends TestCase
{
    #[Test] public function ref_to_enum_has_enum_suffix_and_ref_to_object_does_not(): void
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
                public readonly string \$name;
                public readonly RoleEnum \$role;
                public readonly Address \$address;
                }
                PHP
        );
    }

    #[Test] public function ref_to_enum_and_object_are_both_usable(): void
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
            User::name => 'John',
            User::role => RoleEnum::admin,
            User::address => [
                Address::city => 'Portland',
            ],
        ]);

        self::assertEquals('John', $user->name);
        self::assertEquals(RoleEnum::admin, $user->role);
        self::assertEquals('Portland', $user->address->city);
    }
}
