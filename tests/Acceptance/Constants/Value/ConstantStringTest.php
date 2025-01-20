<?php

namespace Tests\Acceptance\Constants\Value;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ConstantConfig;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;

class ConstantStringTest extends TestCase
{
    #[Test] public function generate(): void
    {
        Engine::generate(
            OpenApi30::adapt(file_get_contents(__DIR__.'/schema.json')),
            Config::from([
                Config::directory => self::$test_dir,
                Config::model => [
                    ModelConfig::constants => [
                        ConstantConfig::comments => true
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