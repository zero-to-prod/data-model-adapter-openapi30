<?php

namespace Tests\Acceptance\Properties\Comment\False;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;
use Zerotoprod\DataModelGenerator\Models\PropertyConfig;

class PropertyHideCommentTest extends TestCase
{
    #[Test] public function generate(): void
    {
        Engine::generate(
            OpenApi30::adapt(file_get_contents(__DIR__.'/schema.json')),
            Config::from([
                Config::directory => self::$test_dir,
                Config::model => [
                    ModelConfig::properties => [
                        PropertyConfig::types => [
                            'number' => 'float'
                        ],
                    ]
                ]
            ])
        );

        self::assertStringEqualsFile(
            expectedFile: self::$test_dir.'/User.php',
            actualString: <<<PHP
                <?php
                class User
                {
                public float \$age;
                }
                PHP
        );
    }
}