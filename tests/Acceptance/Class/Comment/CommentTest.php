<?php

namespace Tests\Acceptance\Class\Comment;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;

class CommentTest extends TestCase
{
    #[Test] public function generate(): void
    {
        Engine::generate(
            OpenApi30::adapt(
                file_get_contents(__DIR__.'/openapi30.json'),
                Config::from([
                    Config::directory => self::$test_dir,
                ])
            )
        );

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/User.php',
            actualString: <<<PHP
                <?php
                /** User */
                class User
                {
                }
                PHP
        );
    }

    #[Test] public function disable_comments(): void
    {
        Engine::generate(
            OpenApi30::adapt(
                file_get_contents(__DIR__.'/openapi30.json'),
                Config::from([
                    Config::directory => self::$test_dir,
                    Config::comments => false,
                ])
            )
        );

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/User.php',
            actualString: <<<PHP
                <?php
                class User
                {
                }
                PHP
        );
    }
}