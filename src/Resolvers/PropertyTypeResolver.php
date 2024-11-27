<?php

namespace Zerotoprod\DataModelAdapterOpenapi30\Resolvers;

use Zerotoprod\DataModelGenerator\Models\Config;
use Zerotoprod\DataModelOpenapi30\Reference;
use Zerotoprod\DataModelOpenapi30\Schema;
use Zerotoprod\Psr4Classname\Classname;

class PropertyTypeResolver
{
    public static function resolve(Reference|Schema $Schema, Config $Config, ?string $enum = null): string
    {
        if ($enum) {
            $types = [$enum];
        } else {
            $types = array_filter(
                array_map(
                    static fn(Reference|Schema $Schema) => self::resolveType($Config, $Schema),
                    array_merge(
                        [$Schema],
                        $Schema->oneOf ?? [],
                        $Schema->anyOf ?? []
                    )
                )
            );
        }

        if ($Schema->nullable) {
            $types[] = 'null';
        }

        return implode('|', array_unique($types));
    }

    private static function resolveType(Config $Config, Reference|Schema $Schema): ?string
    {
        if ($Schema instanceof Reference) {
            return (isset($Config->namespace) ? '\\'.$Config->namespace.'\\' : null).Classname::generate(basename($Schema->ref));
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