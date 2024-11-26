<?php

namespace Tests\Acceptance\Constants\Comments;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ConstantConfig;
use Zerotoprod\DataModelGenerator\Models\PropertyConfig;
use Zerotoprod\DataModelGenerator\Models\Type;

class CommentsTest extends TestCase
{
    #[Test] public function generate(): void
    {
        $Components = OpenApi30::adapt(
            file_get_contents(__DIR__.'/openapi30.json'),
            Config::from([
                Config::directory => self::$test_dir,
                Config::properties => [
                    PropertyConfig::types => [
                        'int32' => [
                            Type::type => 'string'
                        ],
                    ],
                ],
                Config::comments => false,
            ])
        );

        Engine::generate($Components);

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/User.php',
            actualString: <<<PHP
                <?php
                class User
                {
                public const name = 'name';
                public string \$name;
                }
                PHP
        );
    }

    #[Test] public function disable_comment_constant(): void
    {
        $Components = OpenApi30::adapt(
            file_get_contents(__DIR__.'/openapi30.json'),
            Config::from([
                Config::directory => self::$test_dir,
                Config::properties => [
                    PropertyConfig::types => [
                        'int32' => [
                            Type::type => 'string'
                        ],
                    ],
                ],
                Config::constants => [
                    ConstantConfig::exclude_comments => true
                ],
            ])
        );

        Engine::generate($Components);

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/User.php',
            actualString: <<<PHP
                <?php
                class User
                {
                public const name = 'name';
                public string \$name;
                }
                PHP
        );
    }

    #[Test] public function enable_comment_constant(): void
    {
        $Components = OpenApi30::adapt(
            file_get_contents(__DIR__.'/openapi30.json'),
            Config::from([
                Config::directory => self::$test_dir,
                Config::properties => [
                    PropertyConfig::types => [
                        'int32' => [
                            Type::type => 'string'
                        ],
                    ],
                ],
                Config::constants => [
                    ConstantConfig::exclude_comments => false
                ],
                Config::namespace => 'App\\DataModels'
            ])
        );

        Engine::generate($Components);

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/User.php',
            actualString: <<<PHP
                <?php
                namespace App\DataModels;
                class User
                {
                /** @see \$name */
                public const name = 'name';
                public string \$name;
                }
                PHP
        );
    }

    #[Test] public function enable_comment_config(): void
    {
        $Components = OpenApi30::adapt(
            file_get_contents(__DIR__.'/openapi30.json'),
            Config::from([
                Config::directory => self::$test_dir,
                Config::properties => [
                    PropertyConfig::types => [
                        'int32' => [
                            Type::type => 'string'
                        ],
                    ],
                ],
                ConstantConfig::exclude_comments => true,
                Config::constants => [
                    ConstantConfig::exclude_comments => false
                ],
            ])
        );

        Engine::generate($Components);

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/User.php',
            actualString: <<<PHP
                <?php
                class User
                {
                /** @see \$name */
                public const name = 'name';
                public string \$name;
                }
                PHP
        );
    }
}