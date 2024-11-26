<?php

namespace Acceptance\All;

use PHPUnit\Framework\Attributes\Test;
use Tests\generated\AcceptPaymentDisputeRequest;
use Tests\generated\ReturnAddress;
use Tests\generated\FileEvidence;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;

class AllTest extends TestCase
{
    #[Test] public function generate(): void
    {
        $Components = OpenApi30::adapt(
            file_get_contents(__DIR__.'/openapi30.json'),
            Config::from([
                Config::directory => self::$test_dir,
                Config::properties => [],
                Config::namespace => 'Tests\\generated',
                Config::model => [
                    ModelConfig::use_statements => ['use \\Zerotoprod\\DataModel\\DataModel;']
                ]
            ])
        );

        Engine::generate($Components);

        $AcceptPaymentDisputeRequest = AcceptPaymentDisputeRequest::from([
            AcceptPaymentDisputeRequest::returnAddress => [
                ReturnAddress::city => 'city',
                ReturnAddress::files => [
                    [FileEvidence::fileId => 'fileId']
                ]
            ]
        ]);

        self::assertEquals('city', $AcceptPaymentDisputeRequest->returnAddress->city);
        self::assertEquals('fileId', $AcceptPaymentDisputeRequest->returnAddress->files[0]->fileId);
    }
}