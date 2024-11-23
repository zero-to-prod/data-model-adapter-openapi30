<?php

namespace Zerotoprod\DataModelAdapterOpenapi30;

use Zerotoprod\DataModelGenerator\Models\Components;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\Model;
use Zerotoprod\DataModelOpenapi30\OpenApi;
use Zerotoprod\Psr4Classname\Classname;

class OpenApi30
{
    public static function adapt(string $open_api_30_schema, Config $Config): Components
    {
        $OpenApi = OpenApi::from(json_decode($open_api_30_schema, true));

        $Models = array_map(function ($key, $value) {
            return [
                Model::filename => Classname::generate($key, '.php'),
            ];
        },
            array_keys($OpenApi->components->schemas),
            array_values($OpenApi->components->schemas)
        );


        return Components::from([
            Components::Config => $Config,
            Components::Models => $Models
        ]);
    }
}