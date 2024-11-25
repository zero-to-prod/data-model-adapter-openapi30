<?php

namespace Zerotoprod\DataModelAdapterOpenapi30\Resolvers;

use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelOpenapi30\Reference;
use Zerotoprod\DataModelOpenapi30\Schema;

class PropertyTypeResolver
{
    public static function resolve(Schema $Schema, Config $Config): string
    {
        $types = array_filter(
            array_map(
                static fn(Schema $Schema) => self::resolveType($Config, $Schema),
                array_merge(
                    [$Schema],
                    $Schema->oneOf ?? [],
                    $Schema->anyOf ?? []
                )
            )
        );

        if ($Schema->nullable) {
            $types[] = 'null';
        }

        return implode('|', array_unique($types));
    }

    private static function resolveType(Config $Config, Schema|Reference $Schema): ?string
    {
        if ($Schema instanceof Reference) {
            return null;
        }

        return $Config->properties->types[$Schema->format]->type
            ?? match ($Schema->type) {
                'number' => 'float',
                'integer' => 'int',
                'boolean' => 'bool',
                default => $Schema->type,
            };
    }
}