<?php

namespace Zerotoprod\DataModelAdapterOpenapi30\Resolvers;

use Zerotoprod\DataModelOpenapi30\Reference;
use Zerotoprod\DataModelOpenapi30\Schema;
use Zerotoprod\Psr4Classname\Classname;

/**
 * @link https://github.com/zero-to-prod/data-model-adapter-openapi30
 */
class PropertyTypeResolver
{
    /**
     * @link https://github.com/zero-to-prod/data-model-adapter-openapi30
     */
    public static function resolve(Reference|Schema $Schema, ?string $enum = null): array
    {
        if ($enum) {
            $types = [$enum];
        } else {
            $types = array_filter(
                array_map(
                    static fn(Reference|Schema $Schema) => self::resolveType($Schema),
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

        return array_unique($types);
    }

    private static function resolveType(Reference|Schema $Schema): ?string
    {
        if ($Schema instanceof Reference) {
            return Classname::generate(basename($Schema->ref));
        }

        return $Schema->type;
    }
}