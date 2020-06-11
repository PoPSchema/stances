<?php

declare(strict_types=1);

namespace PoP\Stances;

class Environment
{
    public const STANCE_LIST_DEFAULT_LIMIT = 'STANCE_LIST_DEFAULT_LIMIT';
    public const STANCE_LIST_MAX_LIMIT = 'STANCE_LIST_MAX_LIMIT';

    public static function addStanceTypeToCustomPostUnionTypes(): bool
    {
        return isset($_ENV['ADD_STANCE_TYPE_TO_CUSTOM_POST_UNION_TYPES']) ? strtolower($_ENV['ADD_STANCE_TYPE_TO_CUSTOM_POST_UNION_TYPES']) == "true" : false;
    }
}
