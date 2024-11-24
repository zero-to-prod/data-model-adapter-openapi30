<?php

namespace Zerotoprod\DataModelAdapterOpenapi30;

use Zerotoprod\DataModelGenerator\Models\Components;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\Model;
use Zerotoprod\DataModelGenerator\Models\Property;
use Zerotoprod\DataModelOpenapi30\OpenApi;
use Zerotoprod\Psr4Classname\Classname;

class OpenApi30
{
    public static function adapt(string $open_api_30_schema, Config $Config): Components
    {
        $OpenApi = OpenApi::from(json_decode($open_api_30_schema, true));
        $Models = [];
        foreach ($OpenApi->components->schemas as $name => $Schema) {
            $Models[$name] = [
                Model::filename => Classname::generate($name, '.php'),
                Model::properties => array_map(
                    static fn($property) => [
                        Property::type => $Config->properties->types[$property->format]->type
                            ?? match ($property->type) {
                                'number' => 'float',
                                'integer' => 'int',
                                'boolean' => 'bool',
                                default => $property->type,
                            }.($property->nullable ? '|null' : '')
                    ],
                    $Schema->properties
                ),
            ];
        }

        return Components::from([
            Components::Config => $Config,
            Components::Models => $Models
        ]);
    }
}