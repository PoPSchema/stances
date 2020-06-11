<?php

declare(strict_types=1);

namespace PoP\Stances\TypeResolverPickers\Optional;

use PoP\Content\TypeResolvers\CustomPostUnionTypeResolver;
use PoP\Stances\TypeResolverPickers\AbstractStanceTypeResolverPicker;

class StanceCustomPostTypeResolverPicker extends AbstractStanceTypeResolverPicker
{
    public static function getClassesToAttachTo(): array
    {
        return [
            CustomPostUnionTypeResolver::class,
        ];
    }
}
