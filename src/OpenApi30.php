<?php

namespace Zerotoprod\DataModelAdapterOpenapi30;

use Zerotoprod\DataModelAdapterOpenapi30\Resolvers\PropertyTypeResolver;
use Zerotoprod\DataModelGenerator\Models\Components;
use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelGenerator\Models\Constant;
use Zerotoprod\DataModelGenerator\Models\Model;
use Zerotoprod\DataModelGenerator\Models\Property;
use Zerotoprod\DataModelOpenapi30\OpenApi;
use Zerotoprod\DataModelOpenapi30\Reference;
use Zerotoprod\DataModelOpenapi30\Schema;
use Zerotoprod\Psr4Classname\Classname;

class OpenApi30
{
    public static function adapt(string $open_api_30_schema, Config $Config): Components
    {
        $OpenApi = OpenApi::from(json_decode($open_api_30_schema, true));
        $Models = [];
        foreach ($OpenApi->components->schemas as $name => $Schema) {
            if ($Schema->type === 'object') {
                $constants = [];
                if (!$Config->exclude_constants) {
                    foreach ($Schema->properties as $property_name => $PropertySchema) {
                        if ($Config->comments || (isset($Config->constants->exclude_comments) && $Config->constants->exclude_comments)) {
                            $comment = isset($PropertySchema->description)
                                ?
                                <<<PHP
                                /**
                                 * $Schema->description
                                 *
                                 * @see $$property_name
                                 */
                                PHP
                                : <<<PHP
                                /** @see $$property_name */
                                PHP;
                        }

                        $constants[$property_name] = [
                            Constant::comment => $comment ?? null,
                            Constant::value => "'$property_name'"
                        ];
                    }
                }

                $Models[$name] = [
                    Model::comment => isset($Schema->description) ? "/** $Schema->description */" : null,
                    Model::filename => Classname::generate($name, '.php'),
                    Model::properties => array_map(
                        static fn(Schema|Reference $Schema) => [
                            Property::comment => isset($Schema->description)
                            && ($Config->comments
                                || (isset($Config->properties->exclude_comments)
                                    && $Config->properties->exclude_comments))
                                ? <<<PHP
                                /** $Schema->description */
                                PHP
                                : null,
                            Property::type => $Schema instanceof Reference
                                ? (isset($Config->namespace) ? '\\'.$Config->namespace.'\\' : null).Classname::generate(basename($Schema->ref))
                                : PropertyTypeResolver::resolve($Schema, $Config),
                        ],
                        $Schema->properties
                    ),
                    Model::constants => $constants,
                ];
            }
        }

        return Components::from([
            Components::Config => $Config,
            Components::Models => $Models
        ]);
    }
}