<?php

declare(strict_types=1);

namespace PoP\Stances;

class Environment
{
    public static function addStanceTypeToContentEntityUnionTypes(): bool
    {
        return isset($_ENV['ADD_STANCE_TYPE_TO_CONTENTENTITY_UNION_TYPES']) ? strtolower($_ENV['ADD_STANCE_TYPE_TO_CONTENTENTITY_UNION_TYPES']) == "true" : false;
    }
}
