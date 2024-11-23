<?php

namespace Tests\Acceptance\Class;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;

class ClassTest extends TestCase
{
    #[Test] public function generate(): void
    {
        Engine::generate(
            OpenApi30::adapt(
                file_get_contents(__DIR__.'/openapi30.json'),
                Config::from([
                    Config::directory => self::$test_dir
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