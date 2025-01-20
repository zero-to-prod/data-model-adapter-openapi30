<?php

namespace Tests\Acceptance\Constants\Comments;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ConstantConfig;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;

class CommentsTest extends TestCase
{
    #[Test] public function generate(): void
    {
        Engine::generate(
            OpenApi30::adapt(file_get_contents(__DIR__.'/schema.json')),
            Config::from([
                Config::directory => self::$test_dir,
                Config::model => [
                    ModelConfig::constants => [],
                    ModelConfig::properties => []
                ]
            ])
        );

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
        Engine::generate(
            OpenApi30::adapt(file_get_contents(__DIR__.'/schema.json')),
            Config::from([
                Config::directory => self::$test_dir,
                Config::model => [
                    ModelConfig::constants => [],
                    ModelConfig::properties => []
                ]
            ])
        );

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
        Engine::generate(
            OpenApi30::adapt(file_get_contents(__DIR__.'/schema.json')),
            Config::from([
                Config::namespace => 'App\\DataModels',
                Config::directory => self::$test_dir,
                Config::model => [
                    ModelConfig::constants => [
                        ConstantConfig::comments => true,
                    ],
                    ModelConfig::properties => []
                ]
            ])
        );

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
        Engine::generate(
            OpenApi30::adapt(file_get_contents(__DIR__.'/schema.json')),
            Config::from([
                Config::directory => self::$test_dir,
                Config::model => [
                    ModelConfig::constants => [
                        ConstantConfig::comments => true,
                    ],
                    ModelConfig::properties => []
                ]
            ])
        );

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