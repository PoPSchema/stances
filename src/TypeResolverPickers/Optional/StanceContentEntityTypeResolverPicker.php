<?php

declare(strict_types=1);

namespace PoP\Stances\TypeResolverPickers\Optional;

use PoP\Content\TypeResolvers\ContentEntityUnionTypeResolver;
use PoP\Stances\TypeResolverPickers\AbstractStanceTypeResolverPicker;

class StanceContentEntityTypeResolverPicker extends AbstractStanceTypeResolverPicker
{
    public static function getClassesToAttachTo(): array
    {
        return [
            ContentEntityUnionTypeResolver::class,
        ];
    }
}
