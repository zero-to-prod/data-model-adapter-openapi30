<?php

namespace Tests\Acceptance\Class\Namespace;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;

class NamespaceTest extends TestCase
{
    #[Test] public function generate(): void
    {
        Engine::generate(
            OpenApi30::adapt(file_get_contents(__DIR__.'/openapi30.json')),
            Config::from([
                Config::directory => self::$test_dir,
                Config::namespace => 'App\\DataModels',
                Config::model => []
            ])
        );

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/User.php',
            actualString: <<<PHP
                <?php
                namespace App\DataModels;
                class User
                {
                }
                PHP
        );
    }
}