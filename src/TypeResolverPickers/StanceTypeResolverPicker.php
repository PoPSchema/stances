<?php
namespace PoP\Stances\TypeResolverPickers;

use PoP\Stances\Facades\StanceTypeAPIFacade;
use PoP\Stances\TypeResolvers\StanceTypeResolver;
use PoP\Posts\TypeResolvers\PostUnionTypeResolver;
use PoP\ComponentModel\TypeResolverPickers\AbstractTypeResolverPicker;

class StanceTypeResolverPicker extends AbstractTypeResolverPicker
{
    public static function getClassesToAttachTo(): array
    {
        return [
            PostUnionTypeResolver::class,
        ];
    }

    public function getTypeResolverClass(): string
    {
        return StanceTypeResolver::class;
    }

    public function process($resultItemOrID): bool
    {
        $stanceTypeAPI = StanceTypeAPIFacade::getInstance();
        return $stanceTypeAPI->isInstanceOfStanceType($resultItemOrID);
    }
}
