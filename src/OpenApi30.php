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
use Zerotoprod\Psr4VarName\Psr4VarName as VarName;

/**
 * An adapter for the OpenAPI 3.0 for DataModelGenerator
 *
 * @link https://github.com/zero-to-prod/data-model-adapter-openapi30
 */
class OpenApi30
{
    /**
     * @link https://github.com/zero-to-prod/data-model-adapter-openapi30
     */
    public static function adapt(array $open_api_30_schema): Components
    {
        $OpenApi = OpenApi::from($open_api_30_schema);
        $Models = [];
        $Enums = [];
        foreach ($OpenApi->components->schemas as $name => $Schema) {
            if ($Schema->type === 'string' && $Schema->enum) {
                $Enums[$name] = [
                    Enum::comment => $Schema->description ? "/** $Schema->description */" : null,
                    Enum::filename => Classname::generate($name, 'Enum.php'),
                    Enum::backed_type => BackedEnumType::string,
                    Enum::cases => array_map(
                        static fn($value) => [
                            EnumCase::name => $value,
                            EnumCase::value => "'$value'"
                        ],
                        $Schema->enum
                    ),
                ];
                continue;
            }

            if (in_array($Schema->type, ['integer', 'number', 'boolean', 'string'], true)) {
                continue;
            }

            $constants = [];
            if ($Schema->type === 'object') {
                foreach ($Schema->properties as $property_name => $PropertySchema) {
                    $psr_name = VarName::generate($property_name);
                    $comment = isset($PropertySchema->description)
                        ?
                        <<<PHP
                        /**
                         * $PropertySchema->description
                         *
                         * @see $$psr_name
                         */
                        PHP
                        : <<<PHP
                        /** @see $$psr_name */
                        PHP;

                    $constants[$psr_name] = [
                        Constant::comment => $comment,
                        Constant::value => "'$property_name'",
                        Constant::type => 'string'
                    ];
                }
            }

            $Models[$name] = [
                Model::comment => $Schema->description ? "/** $Schema->description */" : null,
                Model::filename => Classname::generate($name, '.php'),
                Model::properties => array_combine(
                    array_map(static fn($k) => VarName::generate($k), array_keys($Schema->properties)),
                    array_map(
                        static function (string $property_name, Schema|Reference $Schema) use ($OpenApi, &$Enums, $name) {
                            $parentSchema = $OpenApi->components->schemas[$name];
                            $propertyData = [
                                Property::attributes => [],
                                Property::comment => null,
                                Property::types => null,
                                Property::required => in_array($property_name, $parentSchema->required, true),
                            ];

                            if (isset($Schema->type, $Schema->items->ref) && $Schema->type === 'array') {
                                $class = Classname::generate(basename($Schema->items->ref));
                                $propertyData[Property::attributes] = [
                                    "#[\\Zerotoprod\\DataModel\\Describe(['cast' => [\\Zerotoprod\\DataModelHelper\\DataModelHelper::class, 'mapOf'], 'type' => $class::class])]"
                                ];

                                $docBlockParts = [];
                                if ($Schema->description) {
                                    $docBlockParts[] = $Schema->description;
                                }
                                $docBlockParts[] = "@var array<int|string, $class>";

                                $propertyData[Property::comment] = "/** \n * ".implode("\n * ", $docBlockParts)."\n */";
                            }

                            if (isset($Schema->type) && $Schema->type === 'string' && $Schema->enum) {
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
                                        $Schema->enum
                                    ),
                                ];
                            }

                            if (!$propertyData[Property::comment] && isset($Schema->description)) {
                                $propertyData[Property::comment] = "/** $Schema->description */";
                            }

                            if (isset($Schema->ref)) {
                                $refName = basename($Schema->ref);
                                $refSchema = $OpenApi->components->schemas[$refName] ?? null;
                                if ($refSchema && $refSchema->type === 'string' && $refSchema->enum) {
                                    $propertyData[Property::types] = [Classname::generate($refName).'Enum'];
                                } elseif ($refSchema && in_array($refSchema->type, ['integer', 'number', 'boolean', 'string'], true)) {
                                    $propertyData[Property::types] = PropertyTypeResolver::resolve($refSchema);
                                } elseif ($refSchema && $refSchema->type === 'object' && !$refSchema->properties) {
                                    $propertyData[Property::types] = ['array'];
                                } else {
                                    $propertyData[Property::types] = [Classname::generate($refName)];
                                }
                                if (!$propertyData[Property::comment] && isset($refSchema->description)) {
                                    $propertyData[Property::comment] = "/** $refSchema->description */";
                                }
                            } else {
                                $propertyData[Property::types] = PropertyTypeResolver::resolve($Schema, $enum ?? null);
                            }

                            $psr_name = VarName::generate($property_name);
                            if ($psr_name !== $property_name) {
                                $fromParam = "'from' => self::$psr_name";
                                $merged = false;
                                foreach ($propertyData[Property::attributes] as $i => $attr) {
                                    if (str_contains($attr, '\\Zerotoprod\\DataModel\\Describe(')) {
                                        $pos = strrpos($attr, '])]');
                                        $propertyData[Property::attributes][$i] = substr($attr, 0, $pos).", $fromParam])]";
                                        $merged = true;
                                        break;
                                    }
                                }
                                if (!$merged) {
                                    $propertyData[Property::attributes][] = "#[\\Zerotoprod\\DataModel\\Describe([$fromParam])]";
                                }
                            }

                            return $propertyData;
                        },
                        array_keys($Schema->properties),
                        $Schema->properties
                    )
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