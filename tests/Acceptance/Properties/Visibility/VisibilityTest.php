<?php

namespace Tests\Acceptance\Properties\Visibility;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\PropertyConfig;
use Zerotoprod\DataModelGenerator\Models\Visibility;

class VisibilityTest extends TestCase
{
    #[Test] public function generate(): void
    {
        Engine::generate(
            OpenApi30::adapt(
                file_get_contents(__DIR__.'/openapi30.json'),
                Config::from([
                    Config::directory => self::$test_dir,
                    Config::properties => [
                        PropertyConfig::visibility => Visibility::protected
                    ],
                    Config::exclude_constants => true,
                ])
            )
        );

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/User.php',
            actualString: <<<PHP
                <?php
                class User
                {
                protected float \$age;
                }
                PHP
        );
    }
}