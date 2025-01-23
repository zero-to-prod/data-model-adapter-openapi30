<?php

namespace Tests\Acceptance\All;

use PHPUnit\Framework\Attributes\Test;
use Tests\generated\AcceptPaymentDisputeRequest;
use Tests\generated\FileEvidence;
use Tests\generated\OrderStatusEnum;
use Tests\generated\ReturnAddress;
use Tests\TestCase;
use Zerotoprod\DataModelAdapterOpenapi30\OpenApi30;
use Zerotoprod\DataModelGenerator\Engine;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\ConstantConfig;
use Zerotoprod\DataModelGenerator\Models\ModelConfig;
use Zerotoprod\DataModelGenerator\Models\PropertyConfig;

class AllTest extends TestCase
{
    #[Test] public function generate(): void
    {
        Engine::generate(
            OpenApi30::adapt(json_decode(file_get_contents(__DIR__.'/schema.json'), true)),
            Config::from([
                Config::model => [
                    ModelConfig::directory => self::$test_dir,
                    ModelConfig::namespace => 'Tests\\generated',
                    ModelConfig::constants => [
                        ConstantConfig::comments => true,
                    ],
                    ModelConfig::properties => [
                        PropertyConfig::types => [
                            'integer' => 'int'
                        ]
                    ],
                    ModelConfig::use_statements => [
                        'use \\Zerotoprod\\DataModel\\DataModel;'
                    ],
                ]
            ])
        );

        $AcceptPaymentDisputeRequest = AcceptPaymentDisputeRequest::from([
            AcceptPaymentDisputeRequest::returnAddress => [
                ReturnAddress::city => 'city',
                ReturnAddress::files => [
                    [FileEvidence::fileId => 'fileId'],
                ],
                ReturnAddress::OrderStatus => OrderStatusEnum::Unshipped
            ]
        ]);

        self::assertEquals('city', $AcceptPaymentDisputeRequest->returnAddress->city);
        self::assertEquals('fileId', $AcceptPaymentDisputeRequest->returnAddress->files[0]->fileId);
        self::assertEquals(OrderStatusEnum::Unshipped, $AcceptPaymentDisputeRequest->returnAddress->OrderStatus);
    }
}