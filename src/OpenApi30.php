<?php

namespace Zerotoprod\DataModelAdapterOpenapi30;

use Zerotoprod\DataModelAdapterOpenapi30\Resolvers\PropertyTypeResolver;
use Zerotoprod\DataModelGenerator\Models\BackedEnumType;
use Zerotoprod\DataModelGenerator\Models\Components;
use Zerotoprod\DataModelGenerator\Models\Constant;
use Zerotoprod\DataModelGenerator\Models\Enum;
use Zerotoprod\DataModelGenerator\Models\EnumCase;
use Zerotoprod\DataModelGenerator\Models\Model;
use Zerotoprod\DataModelGenerator\Models\Property;
use Zerotoprod\DataModelOpenapi30\OpenApi;
use Zerotoprod\DataModelOpenapi30\Reference;
use Zerotoprod\DataModelOpenapi30\Schema;
use Zerotoprod\Psr4Classname\Classname;

class OpenApi30
{
    public static function adapt(string $open_api_30_schema): Components
    {
        $OpenApi = OpenApi::from(json_decode($open_api_30_schema, true));
        $Models = [];
        $Enums = [];
        foreach ($OpenApi->components->schemas as $name => $Schema) {
            if ($Schema->type === 'object') {
                $constants = [];
                foreach ($Schema->properties as $property_name => $PropertySchema) {
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

                    $constants[$property_name] = [
                        Constant::comment => $comment,
                        Constant::value => "'$property_name'"
                    ];
                }
            }

            $Models[$name] = [
                Model::comment => $Schema->description ? "/** $Schema->description */" : null,
                Model::filename => Classname::generate($name, '.php'),
                Model::properties => array_combine(
                    array_keys($Schema->properties),
                    array_map(
                        static function (string $property_name, Schema|Reference $Schema) use (&$Enums) {
                            $is_nested = isset($Schema->type) && $Schema->type === 'array' && $Schema instanceof Reference;
                            $comment = $Schema->description
                                ? <<<PHP
                                /** $Schema->description */
                                PHP
                                : null;
                            if ($is_nested) {
                                $class = Classname::generate(basename($Schema->items->ref));
                                $describe =
                                    ["#[\\Zerotoprod\\DataModel\\Describe(['cast' => [\\Zerotoprod\\DataModelHelper\\DataModelHelper::class, 'mapOf'], 'type' => $class::class])]"];

                                $comment = $Schema->description
                                    ? <<<PHP
                                        /** 
                                         * $Schema->description 
                                         * @var array<int|string, $class>
                                         */
                                        PHP
                                    : <<<PHP
                                        /** @var array<int|string, $class> */
                                        PHP;
                            }
                            if ($Schema->type === 'string' && $Schema->enum) {
                                $enum = Classname::generate(basename($property_name)).'Enum';
                                $Enums[$property_name] = [
                                    Enum::comment => $Schema->description ? "/** $Schema->description */" : null,
                                    Enum::filename => Classname::generate($property_name, 'Enum.php'),
                                    Enum::backed_type => BackedEnumType::string,
                                    Enum::cases => array_map(
                                        static fn($value) => [
                                            EnumCase::name => $value,
                                            EnumCase::value => "'$value'"
                                        ],
                                        $Schema->enum,
                                    ),
                                ];
                            }
                            return [
                                Property::attributes => $describe ?? [],
                                Property::comment => $Schema->description
                                    ? $comment
                                    : null,
                                Property::type => isset($Schema->ref)
                                    ? [Classname::generate(basename($Schema->ref))]
                                    : PropertyTypeResolver::resolve($Schema, $enum ?? null),
                            ];
                        },
                        array_keys($Schema->properties),
                        $Schema->properties
                    ),
                ),
                Model::constants => $constants,
            ];
        }

        return Components::from([
            Components::Models => $Models,
            Components::Enums => $Enums,
        ]);
    }
}