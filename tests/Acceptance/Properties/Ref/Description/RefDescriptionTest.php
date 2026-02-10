<?php

namespace Tests\Acceptance\Properties\Ref\Description;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;
use Zerotoprod\DataModelGenerator\Models\PropertyConfig;

class RefDescriptionTest extends TestCase
{
    #[Test] public function ref_descriptions_appear_in_property_docblocks(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                    ModelConfig::properties => [
                        PropertyConfig::comments => true,
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
                /** domain POJO to represent AuthenticationKey */
                public APIKey \$apiKey;
                /** A user role */
                public RoleEnum \$role;
                /** Milliseconds since the unix epoch */
                public integer \$created_at;
                }
                PHP
        );
    }

    #[Test] public function ref_descriptions_hidden_when_comments_disabled(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                    ModelConfig::properties => [
                        PropertyConfig::comments => false,
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
                public APIKey \$apiKey;
                public RoleEnum \$role;
                public integer \$created_at;
                }
                PHP
        );
    }
}
