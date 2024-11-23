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
            $properties = [];
            if ($Schema->properties) {
                foreach ($Schema->properties as $property_name => $schema) {
                    $properties[$property_name] = [
                        Property::type => $schema->type,
                    ];
                }
            }
            $model[Model::filename] = Classname::generate($name, '.php');
            $model[Model::properties] = $properties;

            $Models[] = $model;
        }

        return Components::from([
            Components::Config => $Config,
            Components::Models => $Models
        ]);
    }
}